<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Events\TestEvent;
use App\Jobs\TestJobs;
use App\Mail\TestMail;
use Illuminate\Support\Facades\Mail;
use App\Exceptions\TestException;
use App\Models\Test;
use Illuminate\Support\Facades\Gate;

class TestController extends Controller
{

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = new User;
        $user->name = "太郎";
        $user->email = $user->email = fake()->unique()->safeEmail();;
        $user->password = Hash::make('password123');

        // ポリシーの認可
        Gate::authorize('viewAny', [Test::class, 'test category']);

        // observer実行
        $user->save();

        // イベント実行
        TestEvent::dispatch($user);
        // event(new TestCompleted($user)); これでもOK

        // job実行
        TestJobs::dispatch($user);
        // TestEvent::dispatch($user)->afterCommit(); DBのトランザクションがコミットされたら実行

        // メール通知
        Mail::to('hoge@example.com')->send(new TestMail($user));

        return view('test.index');
    }

    /**
     * Error test page.
     *
    * @return \Illuminate\Http\Response
    */
    public function error()
    {
        $user = new User;
        $user->name = "太郎";
        throw new TestException('Operation error', 101, ['user_name' => $user->name]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
