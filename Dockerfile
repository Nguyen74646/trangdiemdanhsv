FROM php:8.1-fpm

# Cài các gói cơ bản
RUN apt-get update \
    && apt-get install -y \
    libzip-dev \
    unzip \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Cài extension zip (chỉ thử với 1 extension trước)
RUN docker-php-ext-install -j$(nproc) zip

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