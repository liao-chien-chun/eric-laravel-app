# 使用 PHP 8.1 FPM 作為基底容器
FROM php8.1-fpm 

# Laravel 常用的 PHP 擴充與系統工具
RUN apt-get update && apt-get install -y \
    build-essential \
    libpng-dev \
    libjpeg-dev \
    libonig-dev \
    libxml2-dev \
    zip unzip git curl \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# 從官方 composer image 直接複製 composer 可執行檔進來。
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 設定容器的預設工作目錄（Laravel 專案放置的位置）
WORKDIR /var/www

# 啟動 php-fpm，讓容器跑 Laravel
CMD ["php-fpm"]