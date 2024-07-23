# Utiliser une image PHP avec Apache
FROM php:8.1-apache

# Installer les extensions PHP nécessaires
RUN docker-php-ext-install pdo pdo_mysql

# Installer Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copier les fichiers de l'application dans le conteneur
COPY . /var/www/html

# Changer les permissions
RUN chown -R www-data:www-data /var/www/html \
    && a2enmod rewrite

# Installer les dépendances de Composer
RUN composer install --no-interaction --optimize-autoloader

# Exposer le port 80
EXPOSE 80

# Démarrer Apache
CMD ["apache2-foreground"]
