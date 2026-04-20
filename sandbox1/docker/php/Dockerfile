#==============================================================================
# Node.js ライブラリ抽出ステージ
#==============================================================================
# Node.js 22 LTS (2026年4月時点のアクティブLTS) からバイナリのみ転送
FROM node:22-slim AS node_lib

#==============================================================================
# 共通ベースイメージ (PHP 8.5 + Apache)
#==============================================================================
# PHP 8.5 は 2026年4月時点の最新安定版 (Laravel 13 最低要件: PHP 8.3以上)
FROM php:8.5-apache AS base

# ----- ビルド引数 -----
# WSL2 一般ユーザーの UID/GID (通常1000) に合わせてホストとのファイル権限を同期
ARG USER_ID=1000
ARG GROUP_ID=1000

# ----- PHP拡張インストーラ -----
# ghcr.io 経由が推奨記法 (Docker Hub レート制限を回避し最新版を確実に取得するため)
COPY --from=ghcr.io/mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/

# ----- システムパッケージ -----
# 開発・運用に必須のパッケージを一括導入しキャッシュをクリーンアップしてレイヤーを最小化
RUN apt-get update && apt-get install -y --no-install-recommends \
    coreutils \
    git \
    gnupg \
    locales \
    supervisor \
    tzdata \
    unzip \
    vim \
    zip \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# ----- PHP拡張機能 -----
# Laravel 13 の動作要件を満たす拡張を一括導入
# [本番環境] xdebug はパフォーマンス劣化の原因となるため本番イメージには含めないこと
RUN install-php-extensions \
    bcmath \
    exif \
    gd \
    intl \
    opcache \
    pcntl \
    pdo_mysql \
    redis \
    xdebug \
    zip

# ----- PHP設定 -----
COPY ./docker/php/php.ini /usr/local/etc/php/php.ini

# ----- Node.js (フロントエンドビルド用) -----
# バイナリのみ選択転送し不要ファイルとの競合を避けてイメージを軽量化
COPY --from=node_lib /usr/local/bin/node /usr/local/bin/node
COPY --from=node_lib /usr/local/lib/node_modules /usr/local/lib/node_modules

# npm / npx のシンボリックリンクを作成しコンテナ内でフロントエンドビルドを可能にする
RUN ln -sf /usr/local/lib/node_modules/npm/bin/npm-cli.js /usr/local/bin/npm \
    && ln -sf /usr/local/lib/node_modules/npm/bin/npx-cli.js /usr/local/bin/npx

# ----- Composer -----
# メジャーバージョン 2 系に固定し breaking change の混入を防止
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# ----- タイムゾーン -----
# ログ出力や日付処理の不整合を防ぐためシステムを日本標準時に固定
RUN ln -sf /usr/share/zoneinfo/Asia/Tokyo /etc/localtime \
    && echo "Asia/Tokyo" > /etc/timezone

# ----- ユーザー権限同期 -----
# www-data の UID/GID をホストと同期し WSL2 環境でのファイル書き込み権限エラーを解消
RUN usermod -u ${USER_ID} www-data \
    && groupmod -g ${GROUP_ID} www-data \
    && chsh -s /bin/bash www-data

# ----- 作業ディレクトリ -----
# アプリケーションディレクトリの所有権を設定し実行時のパーミッションエラーを回避
RUN chown www-data:www-data /var/www \
    && chown -R www-data:www-data /var/www/html

WORKDIR /var/www/html

#==============================================================================
# 統合実行ステージ (Apache + Supervisor)
#==============================================================================
FROM base AS apache_env

# ----- Apache設定 -----
COPY ./docker/php/000-default.conf /etc/apache2/sites-available/000-default.conf

# ----- SSL証明書 -----
# [本番環境] 認証局発行の証明書に差し替えること (ここはローカル用の自己署名証明書)
COPY ./docker/ssl/ssl.crt /etc/ssl/certs/ssl-cert-snakeoil.pem
COPY ./docker/ssl/ssl.key /etc/ssl/private/ssl-cert-snakeoil.key

# リライト・SSL・レスポンスヘッダー操作を可能にするモジュールを有効化
RUN a2enmod rewrite ssl headers

# ----- Supervisord設定 -----
# Apache と Worker を単一コンテナで並行稼働させるためのプロセス管理設定
COPY ./docker/php/worker.conf /etc/supervisor/supervisord.conf

# ----- 起動コマンド -----
# Supervisor をフォアグラウンドで起動し全プロセスのライフサイクルを一元管理
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/supervisord.conf"]
