FROM php:8.1-fpm

# Cài các gói cơ bản
RUN apt-get update \
    && apt-get install -y \
    libzip-dev \
    unzip \
    nginx \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Cài extension zip
RUN docker-php-ext-install -j$(nproc) zip

# Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Sao chép mã nguồn
WORKDIR /var/www
COPY . .

# Sao chép nginx.conf
COPY nginx.conf /etc/nginx/conf.d/default.conf

# Đảm bảo .env.example tồn tại
RUN if [ -f ".env.example" ]; then cp .env.example .env; else echo "Error: .env.example not found" && exit 1; fi

# Cài đặt thư viện PHP
RUN composer install --optimize-autoloader --no-dev --ignore-platform-reqs

# Phân quyền
RUN chown -R www-data:www-data /var/www \
    && chmod -R 775 /var/www/storage

# Tạo script khởi động
RUN echo '#!/bin/sh\n\
/usr/local/sbin/php-fpm -y /usr/local/etc/php-fpm.conf -F &\n\
nginx -g "daemon off;"' > /start.sh \
    && chmod +x /start.sh

# Mở cổng động (Render cung cấp PORT, mặc định 10000)
EXPOSE $PORT

# Chạy script khởi động
CMD ["/start.sh"]