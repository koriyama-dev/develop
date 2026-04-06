FROM php:8.5-apache AS base

# 依存パッケージを先にインストール
RUN apt-get update && apt-get install -y \
    locales libpq-dev libicu-dev libonig-dev libzip-dev \
    libpng-dev libjpeg-dev libfreetype6-dev \
    vim git zip unzip cron supervisor \
 && docker-php-ext-configure gd --with-freetype --with-jpeg \
 && docker-php-ext-install zip pdo_mysql gd bcmath intl exif \
 && pecl install xdebug && docker-php-ext-enable xdebug \
 && apt-get clean && rm -rf /var/lib/apt/lists/*

# Node.js (LTS) & npm
RUN curl -SL https://deb.nodesource.com/setup_lts.x | bash
RUN apt-get -y update \
 && apt-get install -y nodejs \
 && npm install -g npm@latest \
 && apt-get clean \
 && rm -rf /var/lib/apt/lists/*

# 共通設定
RUN ln -sf /usr/share/zoneinfo/Asia/Tokyo /etc/localtime
COPY ./docker/php/php.ini /usr/local/etc/php/
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Xdebugログ用ディレクトリ
RUN mkdir -p /var/log/php \
 && touch /var/log/php/xdebug.log \
 && chmod 777 /var/log/php/xdebug.log

# cronログ用ディレクトリ
RUN mkdir -p /var/log/cron && chmod 777 /var/log/cron

# supervisorログ用ディレクトリ
RUN mkdir -p /var/log/supervisor && chmod 777 /var/log/supervisor

WORKDIR /var/www/html

# Apache用
FROM base AS apache_env
COPY ./docker/php/000-default.conf /etc/apache2/sites-available/000-default.conf
COPY ./docker/ssl/ssl.crt /etc/ssl/certs/ssl-cert-snakeoil.pem
COPY ./docker/ssl/ssl.key /etc/ssl/private/ssl-cert-snakeoil.key
RUN a2enmod rewrite ssl && a2ensite default-ssl
CMD ["apache2-foreground"]

# Cron用
FROM base AS cron_env
COPY ./docker/php/cron-root /etc/cron.d/laravel-cron
RUN chmod 0644 /etc/cron.d/laravel-cron && crontab /etc/cron.d/laravel-cron
CMD ["sh", "-c", "printenv > /etc/environment && cron -f"]

# Supervisor用
FROM base AS supervisor_env
COPY ./docker/php/worker.conf /etc/supervisor/conf.d/worker.conf
CMD ["sh", "-c", "printenv > /etc/environment && /usr/bin/supervisord -n -c /etc/supervisor/supervisord.conf"]
