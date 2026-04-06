# 共通ベースイメージ: PHP 8.5 + Apache
FROM php:8.5-apache AS base

# 1. 必要なOSパッケージをインストール (レイヤーを統合して軽量化)
RUN apt-get update && apt-get install -y --no-install-recommends \
    locales libpq-dev libicu-dev libonig-dev libzip-dev \
    libpng-dev libjpeg-dev libfreetype6-dev \
    vim git zip unzip cron supervisor \
    # PHP拡張モジュールのビルドとインストール
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) zip pdo_mysql gd bcmath intl exif \
    # Xdebugのインストール (peclを使用)
    && pecl install xdebug && docker-php-ext-enable xdebug \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# www-dataにシェル（bash）を許可する
RUN chsh -s /bin/bash www-data

# 2. Node.js & npm (LTS版) のインストール
RUN curl -SL https://deb.nodesource.com/setup_lts.x | bash \
    && apt-get install -y nodejs \
    && npm install -g npm@latest \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# 3. タイムゾーン、php.ini、Composerの配置
RUN ln -sf /usr/share/zoneinfo/Asia/Tokyo /etc/localtime
COPY ./docker/php/php.ini /usr/local/etc/php/
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 4. 各種ログ用ディレクトリの作成と権限付与
RUN mkdir -p /var/log/php && touch /var/log/php/xdebug.log && chmod 777 /var/log/php/xdebug.log \
    && mkdir -p /var/log/cron && chmod 777 /var/log/cron \
    && mkdir -p /var/log/supervisor && chmod 777 /var/log/supervisor

RUN chown www-data:www-data /var/www
WORKDIR /var/www/html

# --- ステージ: Apache用 ---
FROM base AS apache_env
COPY ./docker/php/000-default.conf /etc/apache2/sites-available/000-default.conf
COPY ./docker/ssl/ssl.crt /etc/ssl/certs/ssl-cert-snakeoil.pem
COPY ./docker/ssl/ssl.key /etc/ssl/private/ssl-cert-snakeoil.key
# RewriteとSSLモジュールを有効化
RUN a2enmod rewrite ssl headers && a2ensite default-ssl
RUN mkdir -p /var/www/html && chown -R www-data:www-data /var/www/html
CMD ["apache2-foreground"]

# --- ステージ: Cron用 ---
FROM base AS cron_env
COPY ./docker/php/cron-root /etc/cron.d/laravel-cron
RUN chmod 0644 /etc/cron.d/laravel-cron && crontab /etc/cron.d/laravel-cron
# 環境変数を引き継いでcronを実行
CMD ["sh", "-c", "printenv > /etc/environment && cron -f"]

# --- ステージ: Supervisor用 ---
FROM base AS supervisor_env
COPY ./docker/php/worker.conf /etc/supervisor/conf.d/worker.conf
# 環境変数を引き継いでsupervisordをフォアグラウンドで実行
CMD ["sh", "-c", "printenv > /etc/environment && /usr/bin/supervisord -n -c /etc/supervisor/supervisord.conf"]
