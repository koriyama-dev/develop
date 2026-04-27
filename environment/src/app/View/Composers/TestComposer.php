<?php

namespace App\View\Composers;

use Illuminate\View\View;
use Illuminate\Http\Request;
use App\Models\User;

// ViewComposerServiceProvider に登録する

class TestComposer
{

    public function __construct(private Request $request)
    {
        //
    }

    /**
     * viewに渡す
     *
     * @param  View $view
     * @return void
     */
    public function compose(View $view)
    {
        $user = new User;
        $user->name = "太郎";

        $view->with([
            'user_name' => $user->name,
        ]);
    }
}
