<?php

namespace App\Notifications;

use App\Models\Task;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;

class TaskStatusChangedNotification extends Notification
{
    public function __construct(
        public Task $task,
        public string $old,
        public string $new
    ) {}


    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'task_id'    => $this->task->id,
            'title'      => $this->task->title,
            'old_status' => $this->old,
            'new_status' => $this->new,
            'updated_at' => $this->task->updated_at?->toISOString(),
        ];
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return (new BroadcastMessage([
            'task_id'    => $this->task->id,
            'title'      => $this->task->title,
            'old_status' => $this->old,
            'new_status' => $this->new,
            'updated_at' => $this->task->updated_at?->toISOString(),
        ]))

            ->onConnection('sync');
    }

    /** Nice, stable type for your Flutter client */
    public function broadcastType(): string
    {
        return 'task.status.updated';
    }
}
