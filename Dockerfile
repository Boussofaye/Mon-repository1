FROM php:8.2-apache

RUN apt-get update && apt-get install -y \
    zip unzip curl libpng-dev libonig-dev libxml2-dev \
    && docker-php-ext-install pdo pdo_mysql mbstring exif pcntl bcmath gd

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY . /var/www/html

RUN composer install --no-dev --optimize-autoloader

# Modifier DocumentRoot vers le dossier public
RUN sed -ri -e 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/*.conf

RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

RUN a2enmod rewrite

EXPOSE 80