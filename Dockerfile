FROM php:8.2-cli

ENV COMPOSER_ALLOW_SUPERUSER=1

# Install dependensi sistem & ekstensi PHP yang dibutuhkan Laravel
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    && docker-php-ext-install \
    pdo_mysql \
    mbstring \
    xml \
    bcmath \
    zip \
    gd \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Ambil Composer dari image resmi
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Salin file proyek
COPY . .

# Install dependensi PHP
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Pastikan folder storage & cache bisa ditulis
RUN chmod -R 775 storage bootstrap/cache

EXPOSE 8080

CMD sh -c "php artisan config:clear && php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=${PORT:-8080}"