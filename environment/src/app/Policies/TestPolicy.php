<?php

namespace App\Policies;

use App\Models\Test;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class TestPolicy
{

    /**
     * 共通の事前チェック
     * 主に管理者（Super Admin）への全権限付与
     * グローバルな禁止事項の適用
     *
     * @param User $user ログイン中のユーザー
     * @param string $ability 実行しようとしているメソッド名 (例: 'update')
     * @return bool|null
     */
    public function before(User $user, string $ability): ?bool
    {
        // 管理者（is_adminプロパティがtrue）なら、全ての操作を無条件で許可
        // if ($user->is_admin) {
        //       return true;
        // }

        // アカウントが凍結されている場合、全ての操作を無条件で拒否
        // if ($user->is_suspended) {
        //     return false;
        // }

        // null を返すと、本来呼び出そうとしていたメソッド（view, update等）の判定に移行する
        return null;
    }

    /**
     * Determine whether the user can view any models.
     */
    // ?Userは未ログインでも受け付ける
    // 第一引数の User クラスは固定、第二引数以降はコントローラー側で配列で渡される
    // Gate::authorize('viewAny', [Test::class, $category]);
    public function viewAny(?User $user, $category = null): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Test $test): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user, Test $test): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Test $test): bool
    {
        return $user->id === $test->id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Test $test): bool
    {
        return $user->id === $test->id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Test $test): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Test $test): bool
    {
        return false;
    }
}
