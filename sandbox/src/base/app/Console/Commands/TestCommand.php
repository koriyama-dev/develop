<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('app:test-command')]
// protected $signature = 'app:test-command';と同義

#[Description('Command description')]
// protected $description = 'Command description';と同義

class TestCommand extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        logs()->info("test command OK!");
    }
}
