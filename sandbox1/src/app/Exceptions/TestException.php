<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TestException extends Exception
{
    /**
     * @param string $message エラーメッセージ
     * @param int $code errorコード
     * @param array<string, mixed> $context ログに含める追加情報
     */
    public function __construct(
        string $message = "不正な操作が行われました",
        int $code = 100,
        private readonly array $context = []
    ) {
        parent::__construct($message, $code);
    }

    /**
     * 例外をログに記録するロジック
     */
    public function report(): void
    {
        logs()->error('Business Logic Error', [
            'message' => $this->getMessage(),
            'code' => $this->getCode(),
            'context' => $this->context,
        ]);
    }

    /**
     * 例外をHTTPレスポンスに変換するロジック
     */
    public function render(Request $request): JsonResponse
    {
        return response()->json(
            [
            'status'  => 'error',
            'code' => $this->getCode(),
            'message' => $this->getMessage(),
            'data'    => $this->context,
            ],
             400,
             options: JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
        );
    }

}
