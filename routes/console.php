<?php

use App\Console\Commands\UpdateInventoryStatus;
use App\Console\Commands\UpdateReceivingStatus;
use App\Console\Commands\UpdateWanrratyStatus;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

Artisan::command('inventory:update-storage', function () {
    $this->call(UpdateInventoryStatus::class);
})->everyMinute();
Artisan::command('warranty:update-storage', function () {
    $this->call(UpdateWanrratyStatus::class);
})->everyMinute();
Artisan::command('receiving:update-status', function () {
    $this->call(UpdateReceivingStatus::class);
})->everyMinute();