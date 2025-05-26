# Étape 1 : Utiliser une image PHP officielle avec Apache
FROM php:8.2-apache

# Étape 2 : Installer les extensions nécessaires
RUN apt-get update && apt-get install -y \
    zip unzip curl libpng-dev libonig-dev libxml2-dev \
    && docker-php-ext-install pdo pdo_mysql mbstring exif pcntl bcmath gd

# Étape 3 : Installer Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Étape 4 : Copier le code de l'application
COPY . /var/www/html

# Étape 5 : Installer les dépendances Laravel
RUN composer install --no-dev --optimize-autoloader

# Étape 6 : Configurer les permissions
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Étape 7 : Activer mod_rewrite
RUN a2enmod rewrite

# Exposer le port
EXPOSE 80