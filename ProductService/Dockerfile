# Use pre-configured PHP image for Laravel (includes pdo_mysql, redis, etc.)
FROM serversideup/php:8.2-fpm-alpine

# Switch to root to change permissions if needed, but serversideup handles this
USER root

# Set working directory
WORKDIR /var/www

# Copy existing application directory contents
COPY --chown=www-data:www-data . /var/www

# Switch to www-data for composer install
USER www-data

# Install dependencies (no network issues here because it only talks to packagist which works)
RUN composer install --no-interaction --no-dev --optimize-autoloader

EXPOSE 9000
CMD ["php-fpm"]
