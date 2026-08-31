<?php

use App\Console\Commands\GenererAlertesCommand;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Alertes opérationnelles + notifications clients en file, chaque matin (§12/§17)
Schedule::command(GenererAlertesCommand::class)->dailyAt('07:00');
