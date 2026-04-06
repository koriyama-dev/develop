<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\Attributes\Tries;
use Illuminate\Queue\Attributes\Timeout;
use Illuminate\Queue\Attributes\Backoff;
use Illuminate\Queue\Attributes\Queue;
use App\Models\User;
use Throwable;

class TestJobs implements ShouldQueue
{

    /**
     * 最大試行回数
     * Backoffの配列が3つの場合、最初の1回 + リトライ3回で合計「4」にすると
     * すべての待ち時間を使い切ることができます。
     */
    public $tries = 4;

    /**
     * ジョブがタイムアウトになるまでの秒数
     */
    public $timeout = 60;

    /**
     * 再試行までの待ち時間（秒）
     * 配列で指定することで、失敗するたびに間隔を広げます。
     */
    public $backoff = [15, 30, 60];

    /**
     * 投入先のキュー名
     * Supervisorの設定 '--queue=high,default,low' に合わせ、最優先の 'high' を指定
     */
    public $queue = 'high';

    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(private User $user)
    {
        //
    }

    /**
     * Execute the job.
     */
    /**
     * 【実行タイミング】
     * Worker（php artisan queue:work）がこのジョブを「実行中」にした瞬間に呼ばれる。
     * dispatch() した時点では、このメソッドはまだ動いていない。
     */

    /*
     * 【キューの生存サイクル（DB上の動き）】
     * 1. 投入 (dispatch):
     * jobsテーブルにレコードが作成される。
     * この時、クラスのデータ（$userなど）はシリアライズ（文字列化）して「payload」に保存される。
     * * 2. 実行開始:
     * Workerがレコードを見つけ、実行フラグを立てる（reserved_at が更新される）。
     * * 3. 終了時の処理（ここがポイント！）:
     * A. 【成功した場合】:
     * jobsテーブルから即座に削除される。（完了）
     * * B. 【失敗したが、リトライ回数が残っている場合】:
     * jobsテーブルに残る。
     * Backoff（待ち時間）の分だけ実行時間を未来にずらして、再度「待機状態」に戻る。
     * * C. 【失敗し、すべてのリトライを使い切った場合】:
     * jobsテーブルから削除される。
     * 代わりに「failed_jobs」テーブルに、エラー内容と共にレコードが移動する。
     */

    public function handle(): void
    {
        logs()->info($this->user->name . "：test job OK!");
    }

    /**
     * ジョブの失敗を処理
     */
    public function failed(?Throwable $exception): void
    {
        // 失敗時の処理
        logs()->error('ジョブが失敗しました：' . $exception->getMessage());
    }

}
