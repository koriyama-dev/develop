# ==============================================================================
# 1. 共通ベースイメージ (PHP 8.5 + Apache)
# ==============================================================================
FROM php:8.5-apache AS base

ENV TZ=Asia/Tokyo

# WSL2(Ubuntu)の一般ユーザーUID/GID(通常1000)に合わせ、権限トラブルを防止
ARG USER_ID=1000
ARG GROUP_ID=1000

# PHP拡張インストーラー (公式推奨の導入手法)
COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/

# 必要なシステムパッケージのインストール
# supervisor: 複数プロセス管理用 / vim: コンテナ内デバッグ用
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

# Laravel 13 / PHP 8.5 に必要な拡張機能を一括インストール
RUN install-php-extensions pdo_mysql gd bcmath intl exif zip opcache xdebug

# カスタム php.ini の設置
COPY ./docker/php/php.ini /usr/local/etc/php/php.ini

# Node.js (LTS) の導入: Viteやnpmスクリプト実行用
RUN curl -fsSL https://deb.nodesource.com/setup_lts.x | bash - \
    && apt-get install -y nodejs \
    && npm install -g npm@latest

# Composer (最新版) の配置
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 権限設定: www-dataユーザーのUID/GIDをホスト(WSL2)側に合わせる
# これにより、ログファイルやキャッシュの書き込み権限エラーを回避
RUN usermod -u ${USER_ID} www-data && groupmod -g ${GROUP_ID} www-data \
    && chsh -s /bin/bash www-data

# タイムゾーンを日本時間に設定
RUN ln -sf /usr/share/zoneinfo/Asia/Tokyo /etc/localtime && \
    echo "Asia/Tokyo" > /etc/timezone

# 作業ディレクトリの所有者を変更
RUN chown www-data:www-data /var/www
WORKDIR /var/www/html

# ==============================================================================
# 2. 統合実行ステージ (Apache + Supervisor)
# ==============================================================================
FROM base AS apache_env

# Apacheの設定 (VirtualHost / SSL / Rewrite)
COPY ./docker/php/000-default.conf /etc/apache2/sites-available/000-default.conf
COPY ./docker/ssl/ssl.crt /etc/ssl/certs/ssl-cert-snakeoil.pem
COPY ./docker/ssl/ssl.key /etc/ssl/private/ssl-cert-snakeoil.key

# Apacheモジュールの有効化 (Laravelのルーティングにrewriteは必須)
RUN a2enmod rewrite ssl headers && a2ensite default-ssl

# --- Supervisorの設定ファイルをコピー ---
# このファイルに Apache, Worker, Scheduler の起動定義を記述します
COPY ./docker/php/worker.conf /etc/supervisor/conf.d/worker.conf

# Laravelのディレクトリ作成と権限の一括付与
RUN mkdir -p /var/www/html && chown -R www-data:www-data /var/www/html

# --- [重要] メインプロセスの指定 ---
# Apache単体ではなく、Supervisorをコンテナの主役(PID 1)として起動します。
# これにより、Supervisorが管理する全プロセスのログが docker logs に集約されます。
CMD ["/usr/bin/supervisord", "-n", "-c", "/etc/supervisor/conf.d/worker.conf"]
