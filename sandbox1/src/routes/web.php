<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TestController;

Route::get('/', function () {
    return view('welcome');
});

// 一覧
// Route::get('/test', [TestController::class, 'index'])->name('test.index');

// 新規作成
// Route::get('/test/create', [TestController::class, 'create'])->name('test.create');

// 新規保存
// Route::post('/test', [TestController::class, 'store'])->name('test.store');

// 詳細
// Route::post('/test/{test}', [TestController::class, 'show'])->name('test.show');

// 編集
// Route::get('/test/{test}/edit', [TestController::class, 'edit'])->name('test.edit');

// 更新
// Route::patch('/test/{test}', [TestController::class, 'update'])->name('test.update');
   // 変更したい項目のみを送信します。送信されなかった他のフィールドは、現在の状態がそのまま維持されます。
// Route::put('/test/{test}', [TestController::class, 'update'])->name('test.update');
   // もし一部のフィールドを省略して送信した場合、サーバー側ではその項目を null やデフォルト値で上書き（欠損）させるのが標準的な仕様

// 削除
// Route::delete('/test/{test}', [TestController::class, 'destroy'])->name('test.destroy');

// 上記の７つのルーティングを下の一つで同じ効果にできる
// php artisan make:controller TestController --resource
// 「php artisan route:list」を実行して確認可能
Route::resource('test', TestController::class);

Route::get('/test-error', [TestController::class, 'error'])->name('test.error');
