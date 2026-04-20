#==============================================================================
# Node.js ライブラリ抽出ステージ
#==============================================================================
FROM node:24-slim AS node_lib

#==============================================================================
# 共通ベースイメージ (PHP 8.5 + Apache)
#==============================================================================
FROM php:8.5-apache AS base

# ----- ビルド引数 -----
# WSL2 (Ubuntu) の一般ユーザー UID/GID に合わせて www-data を同期 (パーミッション問題を防ぐ)
ARG USER_ID=1000
ARG GROUP_ID=1000

# ----- PHP拡張インストーラ -----
# ghcr.io 経由が推奨記法 (Docker Hub レート制限を回避し最新版を確実に取得するため)
COPY --from=ghcr.io/mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/

# ----- システムパッケージ -----
# --no-install-recommends で不要パッケージを排除してイメージサイズを削減
RUN apt-get update && apt-get install -y --no-install-recommends \
    git \
    gnupg \
    locales \
    netcat-openbsd \
    supervisor \
    unzip \
    vim \
    zip \
    tzdata \
    coreutils \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# ----- PHP拡張機能 -----
# Laravel 13 の動作要件を満たす拡張を一括導入
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

# ----- PHP 設定 -----
COPY ./docker/php/php.ini /usr/local/etc/php/php.ini

# ----- Node.js (Active LTS) -----
# PHP コンテナに Node.js をコピー (Vite 等のフロントエンドビルドツール用)
COPY --from=node_lib /usr/local/bin  /usr/local/bin
COPY --from=node_lib /usr/local/lib/node_modules /usr/local/lib/node_modules
COPY --from=node_lib /usr/local/include /usr/local/include
RUN ln -sf /usr/local/lib/node_modules/npm/bin/npm-cli.js /usr/local/bin/npm \
    && ln -sf /usr/local/lib/node_modules/npm/bin/npx-cli.js /usr/local/bin/npx

# ----- Composer -----
# メジャーバージョン 2 系に固定し breaking change の混入を防止
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# ----- タイムゾーン設定 -----
RUN ln -sf /usr/share/zoneinfo/Asia/Tokyo /etc/localtime \
    && echo "Asia/Tokyo" > /etc/timezone

# ----- ユーザー権限設定 -----
# www-data の UID/GID をホスト (WSL2) 側に合わせてボリュームマウント時のパーミッション競合を防ぐ
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
