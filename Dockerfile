FROM php:8.2-apache

# Instalar extensiones necesarias
RUN apt-get update && apt-get install -y \
    unzip \
    libzip-dev \
    libicu-dev \
    && docker-php-ext-install mysqli pdo pdo_mysql zip intl

# Activar mod_rewrite
RUN a2enmod rewrite

# Copiar proyecto al contenedor
COPY . /var/www/html

# Permisos para writable
RUN chown -R www-data:www-data /var/www/html/writable && \
    chmod -R 775 /var/www/html/writable

# Copiar configuración de Apache
COPY ./apache.conf /etc/apache2/sites-available/000-default.conf

EXPOSE 80
