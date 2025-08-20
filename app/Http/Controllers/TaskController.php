<?php

namespace App\Http\Controllers;

use App\Http\Requests\Task\DeleteTaskRequest;
use App\Http\Requests\Task\StoreTaskRequest;
use App\Http\Requests\Task\UpdateTaskRequest;
use App\Http\Resources\TaskResource;
use App\Models\Task;
use App\Services\TaskService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TaskController extends Controller
{
    public function __construct(private TaskService $service){}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $tasks = $this->service->list(
            $request->only(['status', 'title'])
        );

        return TaskResource::collection($tasks);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTaskRequest $request)
    {
        $task = $this->service->create($request->user(),$request->validated(),$request->file('images', []));

        return TaskResource::make($task)->response()->setStatusCode(Response::HTTP_CREATED);
    }


    /**
     * Display the specified resource.
     */
    public function show(Task $task)
    {
        return TaskResource::make($this->service->findOne($task));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTaskRequest $request, Task $task)
    {
        $task = $this->service->update($task, $request->validated(),
            $request->file('images', []),
            $request->input('delete_image_ids', []));

        return TaskResource::make($task);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DeleteTaskRequest $request, Task $task)
    {
        $this->service->delete($task);

        return response()->json([
            'message' => 'Task deleted successfully.',
        ], Response::HTTP_OK);
    }
}
