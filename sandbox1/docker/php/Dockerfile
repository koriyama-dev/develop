#==============================================================================
# 1. Node.js ライブラリ抽出ステージ
#==============================================================================
# Node.js 22 LTS (2026年4月時点のアクティブLTS) からバイナリのみ転送
FROM node:22-slim AS node_lib

#==============================================================================
# 2. 共通ベースイメージ (PHP 8.5 + Apache)
#==============================================================================
FROM php:8.5-apache AS base

# ----- ビルド引数 -----
# WSL2(Ubuntu)の一般ユーザーUID/GID(通常1000)に合わせる (ホストとのファイル権限を同期するため)
ARG USER_ID=1000
ARG GROUP_ID=1000

# ----- システムパッケージ導入 -----
# PHP拡張機能のインストールを簡略化 (依存関係の自動解決とビルド時間短縮のため)
COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/

# 開発および運用に必須のシステムパッケージを一括導入しレイヤーを最小化
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
# Laravel 13の動作要件を満たす拡張機能を一括導入 (型安全なPHP 8.5環境を構築するため)
# [本番環境: xdebugはパフォーマンス劣化の原因となるため本番イメージには含めないこと]
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
# Node.jsバイナリを選択的に転送 (不要なバイナリとの競合を避けイメージを軽量化するため)
COPY --from=node_lib /usr/local/bin/node /usr/local/bin/node
COPY --from=node_lib /usr/local/lib/node_modules /usr/local/lib/node_modules

# npmおよびnpxのシンボリックリンクを作成 (コンテナ内でのフロントエンドビルドを可能にするため)
RUN ln -sf /usr/local/lib/node_modules/npm/bin/npm-cli.js /usr/local/bin/npm \
    && ln -sf /usr/local/lib/node_modules/npm/bin/npx-cli.js /usr/local/bin/npx

# ----- Composer -----
# バージョン系統を2に固定 (breaking changeの混入を防止するため)
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# ----- タイムゾーン -----
# システムのタイムゾーンを設定 (ログ出力や日付処理の不整合を防止するため)
RUN ln -sf /usr/share/zoneinfo/Asia/Tokyo /etc/localtime \
    && echo "Asia/Tokyo" > /etc/timezone

# ----- ユーザー権限同期 -----
# www-dataの権限をホストと同期 (WSL2環境でのファイル書き込み権限エラーを解消するため)
RUN usermod -u ${USER_ID} www-data \
    && groupmod -g ${GROUP_ID} www-data \
    && chsh -s /bin/bash www-data

# ----- 作業ディレクトリ -----
# アプリケーションディレクトリの所有権を設定 (実行時のパーミッションエラーを回避するため)
RUN chown www-data:www-data /var/www \
    && chown -R www-data:www-data /var/www/html

WORKDIR /var/www/html

#==============================================================================
# 3. 統合実行ステージ (Apache + Supervisor)
#==============================================================================
FROM base AS apache_env

# ----- Apache設定 -----
COPY ./docker/php/000-default.conf /etc/apache2/sites-available/000-default.conf

# ----- SSL証明書 -----
# ローカル用自己署名証明書を配置 [本番環境: 認証局発行の証明書に差し替えること]
COPY ./docker/ssl/ssl.crt /etc/ssl/certs/ssl-cert-snakeoil.pem
COPY ./docker/ssl/ssl.key /etc/ssl/private/ssl-cert-snakeoil.key

# 必要なApacheモジュールを有効化 (リライト・SSL・レスポンスヘッダー操作を可能にするため)
RUN a2enmod rewrite ssl headers

# ----- Supervisord設定 -----
# プロセス管理設定を配置 (ApacheとWorkerを単一コンテナで並行稼働させるため)
COPY ./docker/php/worker.conf /etc/supervisor/supervisord.conf

# ----- 起動コマンド -----
# Supervisorをフォアグラウンドで起動 (全プロセスのライフサイクルを一元管理するため)
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/supervisord.conf"]
