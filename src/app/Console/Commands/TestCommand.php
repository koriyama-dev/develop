<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class TestCommand extends Command
{
    /**
     * コマンドの実行方法
     */
    protected $signature = 'app:test-command';

    /**
     * コマンドの説明
     */
    protected $description = 'test-command description';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        logs()->info("test command OK!");

        /**
         * 終了ステータス（Exit Code）を返す
         * 正常終了時は 0 (Command::SUCCESS) を返すのが UNIX/Linux の作法。
         */
        return Command::SUCCESS;
    }
}
