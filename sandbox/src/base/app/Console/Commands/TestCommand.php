<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

class TestCommand extends Command
{
    // コマンドの実行方法
    protected $signature = 'app:test-command';

    // コマンドの説明
    protected $description = 'test-command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        logs()->info("test command OK!");
    }
}
