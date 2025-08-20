<?php

namespace App\Services;

use App\Models\Task;
use App\Models\TaskImage;
use App\Models\User;
use App\Traits\UploadFiles;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class TaskService
{
    use UploadFiles;

    private const DEFAULT_PER_PAGE = 15;
    private const MAX_PER_PAGE = 100;

    private const TASK_IMAGE_DISK = 'public';

    public function list(array $filters = []): LengthAwarePaginator
    {
        $term = isset($filters['q']) ? trim((string)$filters['q']) : null;
        $status = $filters['status'] ?? null;
        $perPage = (int)($filters['per_page'] ?? self::DEFAULT_PER_PAGE);
        $perPage = ($perPage > 0 && $perPage <= self::MAX_PER_PAGE) ? $perPage : self::DEFAULT_PER_PAGE;

        return Task::query()
            ->status($status)
            ->search($term)
            ->with(['images' => fn($q) => $q->select('id','task_id','path','original_name')
                ->orderBy('id')->limit(1)])
            ->orderByDesc('id')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function findOne(Task $task): Task
    {
        return $task->loadMissing('images:id,task_id,path,original_name');
    }

    public function create(User $user, array $data, array $imageFiles = []): Task
    {
        return DB::transaction(function () use ($user, $data, $imageFiles) {
            $task = $user->tasks()->create(Arr::only($data, ['title', 'description', 'status']));

            if ($imageFiles) {
                $this->attachImages($task, $imageFiles);
            }

            return $task->load('images');
        });
    }

    /**
     * Store images and create TaskImage rows (bulk).
     */
    private function attachImages(Task $task, array $files): void
    {
        $dir = "tasks/images";
        $storedMetas = [];

        try {
            foreach ($files as $file) {
                if ($file instanceof UploadedFile) {
                    $storedMetas[] = $this->storeImage($file, $dir, self::TASK_IMAGE_DISK);
                }
            }

            if (!$storedMetas) {
                return;
            }

            $rows = array_map(fn($m) => [
                'task_id' => $task->id,
                'path' => $m['path'],
                'original_name' => $m['original_name'],
                'created_at' => now(),

            ], $storedMetas);

            TaskImage::insert($rows);
        } catch (\Throwable $e) {
            // Roll back DB (handled by transaction) and delete any files we already wrote
            $paths = array_column($storedMetas, 'path');
            if ($paths) {
                Storage::disk(self::TASK_IMAGE_DISK)->delete($paths);
            }
            throw $e;
        }
    }


    public function update(Task $task, array $data, array $newImages = [], array $deleteImageIds = []): Task
    {
        return DB::transaction(function () use ($task, $data, $newImages, $deleteImageIds) {
            // 1) fields
            $payload = Arr::only($data, ['title', 'description', 'status']);
            if ($payload) {
                $task->update($payload);
            }

            // 2) delete selected images
            if ($deleteImageIds) {
                $this->deleteImages($task, $deleteImageIds);
            }

            // 3) append new images
            if ($newImages) {
                $this->attachImages($task, $newImages);
            }

            return $task->load('images:id,task_id,path,original_name');
        });
    }

    private function deleteImages(Task $task, array $ids): void
    {
        $imgs = TaskImage::query()
            ->where('task_id', $task->id)
            ->whereIn('id', $ids)
            ->get(['id', 'path']);

        if ($imgs->isEmpty()) return;

        Storage::disk(self::TASK_IMAGE_DISK)->delete($imgs->pluck('path')->all());
        TaskImage::whereIn('id', $imgs->pluck('id'))->delete();
    }

    public function delete(Task $task): void
    {
        DB::transaction(function () use ($task) {
            $paths = $task->images()->pluck('path')->all();
            if ($paths) Storage::disk(self::TASK_IMAGE_DISK)->delete($paths);
            $task->delete(); // FK cascade removes task_images
        });
    }

}
