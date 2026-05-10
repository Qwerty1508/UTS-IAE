# Use pre-configured PHP + Nginx image for Laravel (Debian based for better stability)
FROM serversideup/php:8.2-fpm-nginx

# Switch to root to install extensions
USER root
RUN install-php-extensions sockets bcmath

# Set working directory
WORKDIR /var/www/html

# Copy existing application directory contents
COPY --chown=www-data:www-data . /var/www/html

# Switch back to www-data for composer and app execution
USER www-data

# Install dependencies (Including dev to avoid "Class not found" for Pail/Sail etc.)
RUN composer install --no-interaction --optimize-autoloader --ignore-platform-reqs

# Serversideup image runs Nginx on 8080 by default
EXPOSE 8080
