FROM php:8.4-fpm-alpine

# Instalar Caddy y dependencias
RUN apk add --no-cache caddy

# Instalar extensiones de PHP
RUN docker-php-ext-install pdo pdo_mysql

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Establecer directorio de trabajo
WORKDIR /var/www/html

# Copiar archivos
COPY . /var/www/html/

# Instalar dependencias
RUN composer install --no-dev --optimize-autoloader

# Crear Caddyfile
RUN echo ':80 {\n\
    root * /var/www/html/public\n\
    php_fastcgi localhost:9000\n\
    file_server\n\
}' > /etc/caddy/Caddyfile

# Permisos
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Script de inicio
RUN echo '#!/bin/sh\n\
php-fpm -D\n\
sleep 2\n\
caddy run --config /etc/caddy/Caddyfile' > /start.sh && chmod +x /start.sh

EXPOSE 80

CMD ["/start.sh"]