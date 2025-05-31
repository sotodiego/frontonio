# Usar la imagen base de PHP con Apache
FROM php:8.0-apache

# Instalar extensiones necesarias para WordPress
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    unzip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd mysqli pdo pdo_mysql zip

# Configurar la carpeta de WordPress
WORKDIR /var/www/html/

COPY --chown=www-data . /var/www/html/

RUN chmod -R 755 /var/www/html/

# Exponer el puerto 80
#EXPOSE 80

# Comando de inicio
#CMD ["apache2-foreground"]

