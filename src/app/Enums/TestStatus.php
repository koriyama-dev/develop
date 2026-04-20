<?php

namespace App\Enums;

/**
 * DBには string 型で保存される
 */
enum TestStatus: string
{
    case PENDING = 'pending';
    case ACTIVE = 'active';
    case SUSPENDED = 'suspended';
    case INACTIVE = 'inactive';

    /**
     * 日本語ラベルを取得
     */
    public function label(): string
    {
        return match($this) {
            self::PENDING => '承認待ち',
            self::ACTIVE => '有効',
            self::SUSPENDED => '停止中',
            self::INACTIVE => '退会済み',
        };
    }

    /**
     * UI表示用の色（CSSクラス等）を取得
     */
    public function color(): string
    {
        return match($this) {
            self::PENDING => 'gray',
            self::ACTIVE => 'green',
            self::SUSPENDED => 'red',
            self::INACTIVE => 'black',
        };
    }
}