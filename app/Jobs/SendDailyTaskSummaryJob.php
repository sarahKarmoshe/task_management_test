<?php

namespace App\Jobs;

use App\Mail\DailyTaskSummaryMail;
use App\Models\Task;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\RateLimited;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendDailyTaskSummaryJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $timeout = 60;

    public function __construct(public int $userId) {}

    public function middleware(): array
    {
        return [
            new RateLimited('mail-summaries'),
        ];
    }

    public function handle(): void
    {
        $user = User::find($this->userId);
        if (! $user || empty($user->email)) {
            return;
        }

        // Counts by status
        $counts = Task::query()
            ->where('user_id', $user->id)
            ->selectRaw('status, COUNT(*) as aggregate')
            ->groupBy('status')
            ->pluck('aggregate', 'status')
            ->toArray();

        Log::info('Daily summary: preparing email', [
            'user_id' => $user->id,
            'email'   => $user->email,
            'counts'  => $counts,
        ]);

        Mail::to($user->email)->queue(
            new DailyTaskSummaryMail($user, $counts)
        );

        Log::info('Daily summary: email queued', [
            'user_id' => $user->id,
            'email'   => $user->email,
        ]);
    }
}
