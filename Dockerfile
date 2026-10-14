FROM composer:2 AS vendor

WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install \
    --no-dev \
    --no-interaction \
    --no-progress \
    --prefer-dist \
    --optimize-autoloader \
    --no-scripts

FROM php:8.4-cli-alpine AS assets

WORKDIR /var/www/html

RUN apk add --no-cache nodejs npm icu-libs libzip oniguruma \
    && apk add --no-cache --virtual .build-dependencies $PHPIZE_DEPS icu-dev libzip-dev oniguruma-dev \
    && docker-php-ext-install bcmath intl mbstring pdo_mysql zip \
    && apk del .build-dependencies

COPY --from=vendor /app/vendor ./vendor
COPY package*.json ./
RUN npm ci
COPY . .
RUN cp .env.example .env \
    && php artisan key:generate --force \
    && npm run build

FROM php:8.4-fpm-alpine AS app

WORKDIR /var/www/html

RUN apk add --no-cache icu-libs libzip oniguruma netcat-openbsd \
    && apk add --no-cache --virtual .build-dependencies $PHPIZE_DEPS icu-dev libzip-dev oniguruma-dev \
    && docker-php-ext-install bcmath intl mbstring opcache pcntl pdo_mysql zip \
    && apk del .build-dependencies

COPY --from=vendor /app/vendor ./vendor
COPY --from=assets /var/www/html/public/build ./public/build
COPY . .
COPY docker/php/opcache.ini /usr/local/etc/php/conf.d/opcache.ini
COPY docker/php/entrypoint.sh /usr/local/bin/entrypoint

RUN chmod +x /usr/local/bin/entrypoint \
    && mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views storage/logs bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache

ENTRYPOINT ["entrypoint"]
CMD ["php-fpm"]

FROM nginx:1.27-alpine AS nginx

WORKDIR /var/www/html
COPY docker/nginx/default.conf /etc/nginx/conf.d/default.conf
COPY . .
COPY --from=assets /var/www/html/public/build ./public/build
