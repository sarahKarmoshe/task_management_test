<?php
// app/Services/Concerns/HandlesUploads.php

namespace App\Traits;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

trait UploadFiles
{
    /**
     * Store one image under given directory on chosen disk.
     * Returns ['path' => '...', 'original_name' => '...'].
     */
    protected function storeImage(UploadedFile $file, string $dir, string $disk = 'public'): array
    {
        // Generate stable unique filename preserving extension
        $ext  = $file->guessExtension() ?: $file->extension();
        $name = Str::uuid()->toString().'.'.$ext;

        // e.g., tasks/123/images/uuid.png
        $path = $file->storeAs($dir, $name, $disk);

        return [
            'path'          => $path,
            'original_name' => $file->getClientOriginalName(),
        ];
    }

    /**
     * @param UploadedFile[] $files
     */
    protected function storeManyImages(array $files, string $dir, string $disk = 'public'): array
    {
        $out = [];
        foreach ($files as $f) {
            if ($f instanceof UploadedFile) {
                $out[] = $this->storeImage($f, $dir, $disk);
            }
        }
        return $out;
    }

    /** Best-effort cleanup for already-stored files if something fails. */
    protected function deleteStoredPaths(array $paths, string $disk = 'public'): void
    {
        if ($paths) {
            Storage::disk($disk)->delete($paths);
        }
    }
}
