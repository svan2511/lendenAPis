# Stage 1: Composer dependencies (build stage - lightweight)
FROM composer:2 AS composer
WORKDIR /app
COPY composer.json composer.lock* ./
RUN composer install --no-dev --optimize-autoloader --prefer-dist --no-scripts --no-interaction

# Stage 2: Final production image (Nginx + PHP-FPM)
FROM richarvey/nginx-php-fpm:php8.3 

# Env vars Render + Laravel ke liye
ENV SKIP_COMPOSER=1
ENV WEBROOT=/var/www/html/public
ENV PHP_ERRORS_STDERR=1
ENV RUN_SCRIPTS=1
ENV REAL_IP_HEADER=1

ENV APP_ENV=production
ENV APP_DEBUG=false
ENV LOG_CHANNEL=stderr

ENV COMPOSER_ALLOW_SUPERUSER=1

# App code copy
COPY . /var/www/html

# Vendor copy from composer stage
COPY --from=composer /app/vendor /var/www/html/vendor

# Permissions set (storage & cache ke liye must)
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Laravel optimizations + migrations + Passport setup
# Yeh sab build time pe chalega (Render build logs mein dikhega)
RUN php artisan config:cache \
    && php artisan route:cache \
    && php artisan view:cache \
    && php artisan migrate --force --no-interaction || echo "Migration skipped or failed (check if DB connected)" \
    && php artisan passport:install --uuids --no-interaction || echo "Passport install skipped or failed (run manually if needed)" \
    && php artisan passport:keys --no-interaction --force || echo "Passport keys generated or skipped"

# Expose port (Render 80 pe handle karega)
EXPOSE 80

# Start (base image ka script chalega)
CMD ["/start.sh"]