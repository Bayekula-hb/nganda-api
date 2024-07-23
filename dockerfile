# Utiliser une image PHP avec Apache
FROM php:8.1-apache

# Installer les extensions PHP et les dépendances requises
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    unzip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd pdo pdo_mysql zip \
    && pecl install xdebug && docker-php-ext-enable xdebug

# Installer Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copier les fichiers de l'application dans le conteneur
COPY . /var/www/html

# Changer les permissions
RUN chown -R www-data:www-data /var/www/html \
    && a2enmod rewrite

# Installer les dépendances de Composer
RUN composer install --no-interaction --optimize-autoloader --verbose

# Exposer le port 80
EXPOSE 80

# Démarrer Apache
CMD ["apache2-foreground"]
