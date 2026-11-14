FROM php:8.2-apache

# Install ekstensi PHP dan dependensi yang dibutuhkan Laravel
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    zip \
    unzip \
    git \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_mysql gd

# Aktifkan mod_rewrite Apache agar routing Laravel (.htaccess) terbaca
RUN a2enmod rewrite

# Ubah DocumentRoot Apache ke folder public Laravel
RUN sed -i 's/\/var\/www\/html/\/var\/www\/html\/public\/g' /etc/apache2/sites-available/000-default.conf

# Ubah port default Apache dari 80 ke 3000 sesuai EthioDeploy
RUN sed -i 's/80/3000/g' /etc/apache2/sites-available/000-default.conf /etc/apache2/ports.conf

# Izinkan override konfigurasi direktori Apache untuk .htaccess
RUN echo '<Directory /var/www/html/public>\n\
    Options Indexes FollowSymLinks\n\
    AllowOverride All\n\
    Require all granted\n\
</Directory>' >> /etc/apache2/apache2.conf

# Salin seluruh file project ke direktori web server
COPY . /var/www/html
WORKDIR /var/www/html

# Berikan izin akses penuh untuk folder storage dan cache Laravel
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

EXPOSE 3000