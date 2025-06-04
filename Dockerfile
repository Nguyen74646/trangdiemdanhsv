FROM php:8.1-fpm

# Cập nhật và cài đặt các gói hệ thống
RUN apt-get update \
    && apt-get install -y --fix-missing \
    apt-utils \
    libzip-dev \
    unzip \
    git \
    curl \
    libonig-dev \
    libxml2-dev \
    libssl-dev \
    libcurl4-openssl-dev \
    pkg-config \
    build-essential \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Cài đặt các extension PHP cần thiết (bỏ gd tạm thời)
RUN docker-php-ext-install -j$(nproc) zip pdo pdo_mysql mbstring xml intl bcmath json

# Bỏ qua mongodb extension nếu không cần thiết
# RUN pecl install mongodb-1.11.0 \
#     && docker-php-ext-enable mongodb

# Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Làm việc trong thư mục project
WORKDIR /var/www
COPY . .

# Đảm bảo .env.example tồn tại
RUN if [ -f ".env.example" ]; then cp .env.example .env; else echo "Error: .env.example not found" && exit 1; fi

# Cài đặt thư viện PHP
RUN composer install --optimize-autoloader --no-dev --ignore-platform-reqs

# Phân quyền
RUN chown -R www-data:www-data /var/www \
    && chmod -R 775 /var/www/storage

EXPOSE 9000
CMD ["php-fpm"]