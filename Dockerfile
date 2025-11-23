FROM php:8.2-apache

# Instalar extensiones y sus dependencias
RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli

# Activamos el módulo rewrite de Apache (muy útil para webs)
RUN a2enmod rewrite

WORKDIR /var/www/html