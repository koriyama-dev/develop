<?php

namespace App\Listeners;

use App\Events\TestCompleted;

class WriteInfoLog
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(TestCompleted $event): void
    {
        logs()->info($event->user->name . "：event WriteInfoLog OK!");
    }

    // 通常Event::listen や $listen プロパティへの記述が必要になる。
    // 「ただしイベントが app/Events ディレクトリにあり、かつリスナーが app/Listeners に正しく配置されていること」 が自動検出の条件なので今回は不要。


}
