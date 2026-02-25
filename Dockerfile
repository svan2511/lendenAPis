# Stage 1: Composer install (lightweight)
FROM composer:2 AS composer
WORKDIR /app
COPY composer.json composer.lock* ./
RUN composer install --no-dev --optimize-autoloader --prefer-dist --no-scripts --no-interaction

# Stage 2: Final image - PHP 8.3 FPM + Nginx
FROM php:8.3-fpm-alpine

# Install system dependencies + Nginx + extensions Laravel needs
RUN apk update && apk add --no-cache \
    nginx \
    supervisor \
    libzip-dev \
    zip \
    unzip \
    git \
    curl \
    && docker-php-ext-install pdo_mysql zip pcntl bcmath gd exif \
    && apk add --no-cache --virtual .build-deps $PHPIZE_DEPS \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && apk del .build-deps

# Nginx config copy (simple Laravel-friendly)
COPY nginx.conf /etc/nginx/http.d/default.conf

# Supervisor config for Nginx + PHP-FPM
COPY supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# App code copy
WORKDIR /var/www/html
COPY . /var/www/html

# Vendor copy from composer stage
COPY --from=composer /app/vendor /var/www/html/vendor

# Permissions (must for Laravel)
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Laravel optimizations + migrations + Passport (build time pe, DB connected hone pe chalega)
RUN php artisan config:cache \
    && php artisan route:cache \
    && php artisan view:cache \
    && php artisan migrate --force --no-interaction || echo "Migrations skipped (DB not ready?)" \
    && php artisan passport:install --uuids --no-interaction --force || echo "Passport install skipped" \
    && php artisan passport:keys --no-interaction --force || echo "Passport keys skipped"

# Expose port (Nginx 80 pe)
EXPOSE 80

# Supervisor start (Nginx + PHP-FPM dono chalenge)
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]