<?php

namespace App\Listeners;

use App\Events\TestEvent;

class TestWriteDebugLog
{
    /**
     * Handle the event.
     */
    public function handle(TestEvent $event): void
    {
        logs()->info($event->user->name . "：test event TestWriteDebugLog OK!");
    }

    /**
     * 通常Event::listen や $listen プロパティへの記述が必要になる。
     * 「ただしイベントが app/Events ディレクトリにあり、かつリスナーが app/Listeners に正しく配置されていること」
     * が自動検出の条件なので今回は不要。
     */
}
