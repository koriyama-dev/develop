<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// cronに設定したプロセスで実行 (毎分実行)
// 「/var/www/html/base/app/Console/Commands/TestCommand.php」を実行
Schedule::command('app:test-command')->everyMinute();
