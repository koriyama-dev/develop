<?php

namespace App\Observers;

use App\Models\User;
use Illuminate\Contracts\Events\ShouldHandleEventsAfterCommit;

class TestObserver implements ShouldHandleEventsAfterCommit // DBがコミットされたときに実行
{

    // retrieved  …データが取得されたとき
    // creating   …データを新しく登録する直前
    // created    …データを新しく登録した直後
    // updating   …データを更新する直前
    // updated    …データを更新した直後
    // saving     …データを保存する直前（creating & updating）
    // saved      …データを保存した直後（created & updated）
    // deleting   …データを削除する直前
    // deleted    …データを削除した直後

    // observerを実行したいモデルに追記
    // #[ObservedBy(TestObserver::class)]

    /**
     * Handle the User "saved" event.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function saved(User $user)
    {
       logs()->info($user->name . "：test observer saved OK!");
    }
}
