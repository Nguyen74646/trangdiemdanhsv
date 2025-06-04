FROM php:8.1-fpm

# Cài các gói hệ thống cần thiết
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
    && docker-php-ext-configure gd --enable-gd \
    && docker-php-ext-install -j$(nproc) gd zip pdo pdo_mysql mbstring xml intl bcmath \
    && pecl install mongodb \
    && docker-php-ext-enable mongodb \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Làm việc trong thư mục project
WORKDIR /var/www
COPY . .

# Đảm bảo .env.example tồn tại
RUN cp .env.example .env || true

# Cài đặt thư viện PHP
RUN composer install --optimize-autoloader --no-dev

# Phân quyền
RUN chown -R www-data:www-data /var/www \
    && chmod -R 775 /var/www/storage

EXPOSE 9000
CMD ["php-fpm"]