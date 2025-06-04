FROM php:8.1-fpm

# Cài đặt các gói hệ thống
RUN apt-get update && apt-get install -y \
    apt-utils \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    libzip-dev \
    unzip \
    git \
    curl \
    libonig-dev \
    libxml2-dev \
    libssl-dev \
    libcurl4-openssl-dev \
    pkg-config \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd zip pdo pdo_mysql mbstring tokenizer xml

# Cài Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Tạo thư mục project và copy mã nguồn
WORKDIR /var/www
COPY . .

# Cài Laravel dependencies
RUN composer install --optimize-autoloader --no-dev

# Gán quyền
RUN chown -R www-data:www-data /var/www \
    && chmod -R 755 /var/www/storage

# Expose port và chạy PHP-FPM
EXPOSE 9000
CMD ["php-fpm"]
