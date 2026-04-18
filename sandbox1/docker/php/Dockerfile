# ==============================================================================
# 1. 共通ベースイメージ (PHP 8.5 + Apache)
# ==============================================================================
FROM node:22-slim AS node_lib
FROM php:8.5-apache AS base

# WSL2(Ubuntu)の一般ユーザーUID/GID(通常1000)に合わせる
ARG USER_ID=1000
ARG GROUP_ID=1000

# PHP拡張インストーラー
COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/

# システムパッケージのインストール
RUN apt-get update && apt-get install -y --no-install-recommends \
    git \
    gnupg \
    locales \
    supervisor \
    unzip \
    vim \
    zip \
    tzdata \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Laravel 13 / PHP 8.5 に必要な拡張機能をインストール
RUN install-php-extensions pdo_mysql gd bcmath intl exif zip opcache xdebug redis pcntl

# カスタム php.ini 設置
COPY ./docker/php/php.ini /usr/local/etc/php/php.ini

# Node.js (LTS) のインストール
COPY --from=node_lib /usr/local/bin /usr/local/bin
COPY --from=node_lib /usr/local/lib/node_modules /usr/local/lib/node_modules
# npm のエントリーポイントを明示的にシンボリックリンク
RUN ln -sf /usr/local/lib/node_modules/npm/bin/npm-cli.js /usr/local/bin/npm
# npx のシンボリックリンク
RUN ln -sf /usr/local/lib/node_modules/npm/bin/npx-cli.js /usr/local/bin/npx

# Composer (最新版) の配置
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 権限設定: www-dataユーザーのUID/GIDをホスト(WSL2)側に合わせる
# これでログファイルやキャッシュの書き込み権限エラーを回避
RUN usermod -u ${USER_ID} www-data && groupmod -g ${GROUP_ID} www-data \
    && chsh -s /bin/bash www-data

# タイムゾーンを日本時間に設定
RUN ln -sf /usr/share/zoneinfo/Asia/Tokyo /etc/localtime && \
    echo "Asia/Tokyo" > /etc/timezone

# 作業ディレクトリの所有者を変更
RUN chown www-data:www-data /var/www && chown -R www-data:www-data /var/www/html
WORKDIR /var/www/html

# ==============================================================================
# 2. 統合実行ステージ (Apache + Supervisor)
# ==============================================================================
FROM base AS apache_env

# Apache設定
COPY ./docker/php/000-default.conf /etc/apache2/sites-available/000-default.conf
COPY ./docker/ssl/ssl.crt /etc/ssl/certs/ssl-cert-snakeoil.pem
COPY ./docker/ssl/ssl.key /etc/ssl/private/ssl-cert-snakeoil.key

# Apacheモジュール有効化
RUN a2enmod rewrite ssl headers && a2ensite default-ssl

# Supervisorの設定
# Apache, Worker, Scheduler の起動定義
COPY ./docker/php/worker.conf /etc/supervisor/supervisord.conf

# メインプロセスの指定
# Apache単体ではなく、Supervisorをコンテナの主役(PID 1)として起動
# これでSupervisorが管理する全プロセスのログが docker logs に集約
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/supervisord.conf"]
