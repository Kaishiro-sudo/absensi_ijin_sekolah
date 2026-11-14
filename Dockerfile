FROM php:8.2-apache

# Install ekstensi PHP & dependensi sistem yang dibutuhkan Laravel
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    zip \
    unzip \
    git \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_mysql gd zip

# Install Composer resmi ke dalam container
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Aktifkan mod_rewrite Apache agar routing Laravel terbaca
RUN a2enmod rewrite

# Ubah DocumentRoot Apache ke folder public Laravel
RUN sed -i 's#/var/www/html#/var/www/html/public#g' /etc/apache2/sites-available/000-default.conf

# Ubah port default Apache dari 80 ke 3000 sesuai EthioDeploy
RUN sed -i 's/80/3000/g' /etc/apache2/sites-available/000-default.conf /etc/apache2/ports.conf

# Izinkan override konfigurasi direktori untuk .htaccess
RUN echo '<Directory /var/www/html/public>\n\
    Options Indexes FollowSymLinks\n\
    AllowOverride All\n\
    Require all granted\n\
</Directory>' >> /etc/apache2/apache2.conf

WORKDIR /var/www/html

# Salin seluruh file project terlebih dahulu
COPY . .

# Jalankan composer install untuk membuat folder vendor secara langsung
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Berikan izin akses penuh untuk folder storage dan cache Laravel
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

EXPOSE 3000