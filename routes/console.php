<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Console\Commands\DispatchDailyTaskSummaries;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command(DispatchDailyTaskSummaries::class)
    ->dailyAt('08:00')
    ->timezone(config('app.timezone'))
    ->evenInMaintenanceMode(); //just while development
