<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\View\Composers\TestComposer;

// bootstrap/providers.phpに追記する
// App\Providers\ViewComposerServiceProvider::class,

class ViewComposerServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        View::composers([
            // 全てのviewに適用
            TestComposer::class => '*'
            // 部分的に渡す場合
            //TestComposer::class => 'test.*'
        ]);
    }
}
