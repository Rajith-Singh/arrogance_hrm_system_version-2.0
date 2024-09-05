<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use App\Console\Commands\SendWeeklyLatecomerEmails;
use Illuminate\Foundation\Console\Kernel;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

Artisan::command('schedule:work', function (Kernel $kernel) {
    $kernel->schedule(
        SendWeeklyLatecomerEmails::class
    )->weekly()->mondays()->at('13:00');
});
