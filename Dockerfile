# Stage 1: Composer dependencies (ignore platform reqs to bypass lock issue)
FROM composer:2 AS composer
WORKDIR /app
COPY composer.json composer.lock* ./
RUN composer install --no-dev --optimize-autoloader --prefer-dist --no-scripts --no-interaction --ignore-platform-reqs

# Stage 2: Final image - PHP 8.3 FPM + Nginx + Supervisor
FROM php:8.3-fpm-alpine

# Install system packages + GD dependencies + extensions
RUN apk update && apk add --no-cache \
    nginx \
    supervisor \
    libzip-dev \
    zip \
    unzip \
    git \
    curl \
    freetype-dev \
    libjpeg-turbo-dev \
    libpng-dev \
    && docker-php-ext-configure gd \
        --with-freetype=/usr/include/ \
        --with-jpeg=/usr/include/ \
    && docker-php-ext-install -j$(nproc) \
        pdo_mysql \
        zip \
        pcntl \
        bcmath \
        gd \
        exif \
    && apk add --no-cache --virtual .build-deps $PHPIZE_DEPS \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && apk del .build-deps

# Copy Nginx config
COPY nginx.conf /etc/nginx/http.d/default.conf

# Copy Supervisor config
COPY supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Set working dir + copy app code
WORKDIR /var/www/html
COPY . /var/www/html

# Copy vendor from composer stage
COPY --from=composer /app/vendor /var/www/html/vendor

# Permissions fix
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Laravel cache (build time safe)
RUN php artisan config:cache \
    && php artisan route:cache \
    && php artisan view:cache

# Expose port
EXPOSE 80

# Start Supervisor
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]