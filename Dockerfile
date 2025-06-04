# Use the official PHP image with Apache
FROM php:8.2-apache

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git unzip zip curl libzip-dev libpng-dev libonig-dev \
    libxml2-dev libcurl4-openssl-dev pkg-config libssl-dev \
    && docker-php-ext-install pdo pdo_mysql zip

# Install MongoDB extension
RUN pecl install mongodb && docker-php-ext-enable mongodb

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Set working directory
WORKDIR /var/www/html

# Copy Laravel app source
COPY . .

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Cài Laravel dependencies
RUN composer install --optimize-autoloader --no-dev

# Cài Node.js và build frontend (Vite, Tailwind, ...)
RUN curl -fsSL https://deb.nodesource.com/setup_18.x | bash - \
    && apt-get install -y nodejs \
    && npm install \
    && npm run build || true

# Laravel permission
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage \
    && chmod -R 755 /var/www/html/bootstrap/cache

# Expose port 80
EXPOSE 80
