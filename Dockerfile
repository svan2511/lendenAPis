# Stage 1: Composer dependencies install (with ignore platform to bypass lock mismatch)
FROM composer:2 AS composer
WORKDIR /app
COPY composer.json composer.lock* ./
RUN composer install --no-dev --optimize-autoloader --prefer-dist --no-scripts --no-interaction --ignore-platform-reqs

# Stage 2: Final production image - PHP 8.3 FPM + Nginx + Supervisor
FROM php:8.3-fpm-alpine

# Install required packages + extensions
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

# Copy Nginx config
COPY nginx.conf /etc/nginx/http.d/default.conf

# Copy Supervisor config
COPY supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Set working dir + copy app
WORKDIR /var/www/html
COPY . /var/www/html

# Copy vendor from composer stage
COPY --from=composer /app/vendor /var/www/html/vendor

# Set permissions (storage & cache writable)
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Laravel cache optimizations (build time pe safe)
RUN php artisan config:cache \
    && php artisan route:cache \
    && php artisan view:cache

# Expose Nginx port
EXPOSE 80

# Start with Supervisor (Nginx + PHP-FPM)
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]