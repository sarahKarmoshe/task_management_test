<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class DailyTaskSummaryMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public array $counts,
    ) {}

    public function build(): self
    {
        return $this->subject('Your Daily Task Summary')
            ->markdown('emails.tasks.daily_summary', [
                'user'        => $this->user,
                'counts'      => $this->counts,
            ]);
    }
}
