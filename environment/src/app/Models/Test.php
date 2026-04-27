<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Prunable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use App\Enums\TestStatus;

/**********************************************
    ※モデル設計ルール
    ・テーブル名：複数形スネークケース (例：tests)
    ・カラム名  ：スネークケース (モデル名を含めない)
    ・モデル名  ：単数形アッパーキャメルケース (例：Test)
    ・主キー名  ：id
    ・外部キー  ：親モデル単数形_id (例：test_id)
**********************************************/
class Test extends Model
{
    // HasFactory: テストデータ作成用
    // SoftDeletes: deleted_atカラムを使用し、レコードを物理削除せず残す
    // Prunable: 一定期間を過ぎた不要データを自動削除する
    use HasFactory, SoftDeletes, Prunable;

    /*************************
     * 基本構成 (テーブル構造)
     **************************/

    /**
     * 使用するDB接続名
     */
    // protected $connection = 'mysql';

    /**
     * 対応するテーブル名
     */
    // protected $table = 'test';

    /**
     * 主キーのカラム名(デフォルト: id)
     */
    // protected $primaryKey = 'test_id';

    /**
     * 主キーのデータ型(デフォルト: int)
     */
    // protected $keyType = 'int';

    /**
     * 主キーが自動インクリメントか否か(デフォルト: true)
     */
    // public $incrementing = true;

    /**
     * created_at, updated_at の自動更新を行うか(デフォルト: true)
     */
    // public $timestamps = true;

    /**
     * 日付保存フォーマット(デフォルト: Y-m-d H:i:s)
     */
    // protected $dateFormat = 'Y-m-d H:i:s';

    /*************************
     * 複数代入 (セキュリティ)
     **************************/

    /**
     * 一括代入を許可する属性(ホワイトリスト)
     * $test->fill($request->all())->save(); 等で利用
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'age',
        'status',
        'options',
    ];

    /**
     * 保存禁止カラム(ブラックリスト)
     */
     // protected $guarded = ['id', 'role'];

    /*************************
     * シリアライズ (JSON/配列変換)
     **************************/

    /**
     * JSON/配列変換時に隠蔽する属性(ブラックリスト)
     */
    protected $hidden = ['password', 'token'];

    /**
     * JSON/配列変換時に含める属性(ホワイトリスト)
     */
    protected $visible = ['name', 'email'];

    /**
     * JSON/配列変換時に含めるアクセサ属性 (Property Hooks：ロジック加工)
     */
    protected $appends = ['full_name'];

    /*************************
     * 型変換・リレーション制御
     **************************/

    protected $casts = []; // 型変換定義 (L13ではcasts()メソッド推奨)

    /**
     * 関連データ（リレーション先）を事前にまとめて取得する
     */
    // protected $with = ['profile'];

    /**
     * 常に件数を取得するリレーション
     */
    // protected $withCount = ['comments'];   // [修正] 常に件数を取得するリレーション

    /**
     * ページネーションのデフォルト件数
     */
    protected $perPage = 50;

    /**
     * このモデルが更新された際、親モデルの updated_at も自動更新する
     */
    // protected $touches = ['user'];

    /**************************
        キャスト設定(型変換)
    **************************/
    protected function casts(): array
    {
        return [
            'status' => TestStatus::class,    // PHP Enumキャスト
            'options' => AsArrayObject::class, // JSONカラムを配列オブジェクトとして操作
            'is_active' => 'boolean',
            'test_at' => 'datetime:Y-m-d',    // フォーマット指定はシリアライズ時のみ有効
        ];
    }

    /**************************
        アクセサ(取り出す時に加工) / ミューテタ(保存する時に加工)：Property Hooks
    **************************/

    // $this->age と $this->attributes['age']の使い分け
    // $this->age               …アクセサやキャストが適用された後の値を返す
    // $this->attributes['age'] …データベースから取得したままの生データを返す
    // 基本的には $this->age を使用すべき
    // $this->attributes を直接操作するのは、アクセサの中でそのカラム自体の生データを参照したい場合
    // （そうしないとアクセサが自分自身を呼び出し続けて無限ループになるため）

    /**
     * age: 初期値を制御するアクセサとミューテタ
     */
    public int $age {
        // DBの値が何であっても、プログラム上では常に『整数（int）』が保証され、nullが『0』に変換される
        get => $this->attributes['age'] ?? 0;
        set(int|null $value) => $this->attributes['age'] = $value ?? 0;
    }

    /**
     * full_name: 読み取り専用の複合属性
     * DBにカラムが存在しない仮想的なプロパティ
     */
    public string $full_name {
        get => $this->first_name . $this->last_name;
    }

    /**************************
        モデルイベント
    **************************/
    protected static function booted(): void
    {
        // 作成時の自動処理
        static::creating(function (Model $test) {
            $test->uuid = str()->uuid();
        });

        // 更新時の自動処理
        static::updating(function (Model $test) {
            $test->test_at = now();
        });
    }

    /**************************
        リレーション (基本)
    **************************/

    /**
     * 1対1: 親(Test)から子(TestSub)へ
     * ⇒ 相手側(test_subs)に「test_id」があること
     */
    public function testSub(): HasOne
    {
        return $this->hasOne(TestSub::class)->withDefault();
    }

    /**
     * 1対多: 親(Test)から子(Profile)へ
     * ⇒ 相手側(profiles)に「test_id」があること
     */
    public function profiles(): HasMany
    {
        return $this->hasMany(Profile::class);
    }

    /**
     * 多対1: 子(Test)から親(User)へ
     * ⇒ 自テーブル(tests)に「user_id」があること
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * 多対多: 中間テーブル経由
     * ⇒ 中間テーブル(rank_test)に「test_id」と「rank_id」があること
     */
    public function ranks(): BelongsToMany
    {
        return $this->belongsToMany(Rank::class)->withTimestamps();
    }

    /**************************
        リレーション (応用)
    **************************/

    /**
     * HasOneThrough: 中間モデルを1つ経由して「1件」取得
     * 例: Test -> User -> UserProfile
     * 実例: サプライヤー(Supplier)が、ユーザー(User)を介して「アカウントログ(Log)」を1件取得する場合など
     */
    public function userProfile(): HasOneThrough
    {
        return $this->hasOneThrough(UserProfile::class, User::class);
    }

    /**
     * HasManyThrough: 中間モデルを1つ経由して「複数件」取得
     * 例: Test -> User -> Post
     * 実例: 国(Country)が、ユーザー(User)を介して「全ての投稿(Post)」を取得する場合など
     */
    public function userPosts(): HasManyThrough
    {
        return $this->hasManyThrough(Post::class, User::class);
    }

    /**
     * MorphTo: 多相リレーション (子側)
     * 自身が「どのモデルに属しているか」を判定
     * 自身のテーブルに imageable_id, imageable_type を持つ
     * 実例: 写真(Image)が、ユーザー(User)のものか投稿(Post)のものかを動的に判定
     */
    public function imageable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * MorphOne: 多相リレーション (親側・1対1)
     * 自身に紐付く「唯一の」汎用モデルを取得
     * 実例: ユーザー(User)が持つ「最新の1枚」のプロフィール画像(Image)
     */
    public function latestImage(): MorphOne
    {
        return $this->morphOne(Image::class, 'imageable')->latestOfMany();
    }

    /**
     * MorphMany: 多相リレーション (親側・1対多)
     * 自身に紐付く「複数の」汎用モデルを取得
     * 実例: 投稿(Post)に紐付く「大量の」コメント(Comment)や添付ファイル
     */
    public function images(): MorphMany
    {
        return $this->morphMany(Image::class, 'imageable');
    }

    /**
     * MorphToMany: 多相リレーション (多対多)
     * 複数のモデル間で共有されるモデル（Tagなど）を取得
     * 実例: 投稿(Post)と動画(Video)の両方で共通して利用される「タグ(Tag)」
     */
    public function tags(): MorphToMany
    {
        return $this->morphToMany(Tag::class, 'tagable');
    }
    /**************************
        スコープ
    **************************/

    /**
     * ローカルスコープ: 検索条件の共通化
     * 使用例: Test::status(TestStatus::Active)->get();
     */
    public function scopeStatus(Builder $query, TestStatus $status): Builder
    {
        return $query->where('status', $status);
    }

    /**************************
        クリーンアップ
    **************************/

    /**
     * Prunable: 古いデータの自動削除条件
     * 実行コマンド: php artisan model:prune
     */
    public function prunable(): Builder
    {
        // 1年以上前のレコードを対象とする例
        return static::where('created_at', '<=', now()->subYear());
    }

}
