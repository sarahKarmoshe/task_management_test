<?php

namespace App\Console\Commands;

use App\Jobs\SendDailyTaskSummaryJob;
use App\Models\User;
use Illuminate\Console\Command;

class DispatchDailyTaskSummaries extends Command
{
    protected $signature = 'tasks:daily-summary {--dry-run : Show who would get an email without dispatching}';
    protected $description = 'Dispatch per-user jobs to email daily task summaries';

    public function handle(): int
    {
        $dry = (bool) $this->option('dry-run');

        $count = 0;
        User::query()
            ->whereNotNull('email')
            ->orderBy('id')
            ->chunkById(100, function ($users) use (&$count, $dry) {
                foreach ($users as $u) {
                    $count++;
                    if ($dry) {
                        $this->line("Would dispatch summary for user #{$u->id} <{$u->email}>");
                        continue;
                    }
                    SendDailyTaskSummaryJob::dispatch($u->id);
                }
            });

        $this->info($dry
            ? "Dry run complete. {$count} user(s) would receive a summary."
            : "Dispatched summaries for {$count} user(s)."
        );

        return self::SUCCESS;
    }
}
