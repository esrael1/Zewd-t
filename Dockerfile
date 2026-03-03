FROM node:20-alpine AS frontend-build
WORKDIR /app/frontend

COPY frontend/package*.json ./
RUN npm ci

COPY frontend/ ./
ARG VITE_API_URL=/api
ENV VITE_API_URL=${VITE_API_URL}
RUN npm run build

FROM composer:2 AS composer-build
WORKDIR /app/backend

COPY backend/composer.json backend/composer.lock ./
RUN composer install \
    --no-dev \
    --prefer-dist \
    --no-interaction \
    --optimize-autoloader \
    --no-scripts

FROM php:8.4-cli-alpine
WORKDIR /app/backend

RUN apk add --no-cache \
    bash \
    postgresql-dev \
    libzip-dev \
    sqlite \
    sqlite-dev \
    && docker-php-ext-install pdo pdo_sqlite pdo_pgsql

COPY --from=composer-build /app/backend/vendor ./vendor
COPY backend/ ./
COPY --from=frontend-build /app/frontend/dist ./public

RUN mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache

ENV APP_ENV=production
ENV APP_DEBUG=false
EXPOSE 10000

CMD sh -lc "php artisan package:discover --ansi && (php artisan storage:link || true) && php artisan config:cache && php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=${PORT:-10000}"