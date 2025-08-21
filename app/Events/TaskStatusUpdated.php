<?php

namespace App\Events;

use App\Models\Task;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TaskStatusUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public Task $task,
        public string $oldStatus,
        public string $newStatus
    ) {}

    // Broadcast to the task owner’s private channel
    public function broadcastOn(): array
    {
        return [new PrivateChannel('users.' . $this->task->user_id)];
    }

    public function broadcastAs(): string
    {
        return 'task.status.updated';
    }

    public function broadcastWith(): array
    {
        return [
            'task_id'    => $this->task->id,
            'title'      => $this->task->title ?? null,
            'old_status' => $this->oldStatus,
            'new_status' => $this->newStatus,
            'updated_at' => $this->task->updated_at?->toISOString(),
        ];
    }
}
