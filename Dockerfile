FROM php:8.2-apache
RUN docker-php-ext-install pdo pdo_mysql

# Ubah port Apache ke 3000
RUN sed -i 's/80/3000/g' /etc/apache2/sites-available/000-default.conf /etc/apache2/ports.conf

COPY . /var/www/html
WORKDIR /var/www/html
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
EXPOSE 3000