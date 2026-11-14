FROM php:8.2-apache

# Install ekstensi PHP & dependensi sistem
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    zip \
    unzip \
    git \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_mysql gd

# Install Composer resmi ke dalam container
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Aktifkan mod_rewrite Apache
RUN a2enmod rewrite

# Ubah DocumentRoot Apache ke folder public Laravel
RUN sed -i 's#/var/www/html#/var/www/html/public#g' /etc/apache2/sites-available/000-default.conf

# Ubah port default Apache ke 3000 untuk EthioDeploy
RUN sed -i 's/80/3000/g' /etc/apache2/sites-available/000-default.conf /etc/apache2/ports.conf

# Izinkan override konfigurasi direktori untuk .htaccess
RUN echo '<Directory /var/www/html/public>\n\
    Options Indexes FollowSymLinks\n\
    AllowOverride All\n\
    Require all granted\n\
</Directory>' >> /etc/apache2/apache2.conf

WORKDIR /var/www/html

# Salin file composer terlebih dahulu agar dependensi ter-cache dengan baik
COPY composer.json composer.lock* ./

# Jalankan composer install untuk mendownload vendor
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Salin seluruh sisa file project
COPY . .

# Berikan izin akses untuk folder storage dan cache
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

EXPOSE 3000