FROM php:8.2-apache

# Habilitar extensiones necesarias
RUN apt-get update && apt-get install -y \
    unzip \
    libzip-dev \
    && docker-php-ext-install mysqli pdo pdo_mysql zip

# Habilitar mod_rewrite
RUN a2enmod rewrite

# Copiar el proyecto al contenedor
COPY . /var/www/html

# Establecer permisos correctos
RUN chown -R www-data:www-data /var/www/html/writable

# Configuración de Apache
COPY ./apache.conf /etc/apache2/sites-available/000-default.conf

EXPOSE 80
