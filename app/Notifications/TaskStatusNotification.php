<?php

namespace App\Notifications;

use App\Domains\Task\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class TaskStatusNotification extends Notification
{
    use Queueable;

    public function __construct(
        public readonly Task   $task,
        public readonly string $title,
        public readonly string $message,
        public readonly string $type = 'info',   // info | success | warning | danger
        public readonly ?string $url = null,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'task_id'    => $this->task->id,
            'task_title' => $this->task->title,
            'department' => $this->task->department->name ?? '—',
            'title'      => $this->title,
            'message'    => $this->message,
            'type'       => $this->type,
            'url'        => $this->url ?? route('tasks.show', $this->task),
        ];
    }
}
