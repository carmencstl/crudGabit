FROM php:8.4-fpm-alpine

# Instalar Nginx
RUN apk add --no-cache nginx

# Instalar extensiones de PHP
RUN docker-php-ext-install pdo pdo_mysql

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copiar archivos
WORKDIR /var/www/html
COPY . /var/www/html/

# Instalar dependencias
RUN composer install --no-dev --optimize-autoloader

# Crear directorios
RUN mkdir -p /run/nginx

# Configurar PHP-FPM CON USUARIO
RUN echo '[global]' > /usr/local/etc/php-fpm.conf && \
    echo 'error_log = /proc/self/fd/2' >> /usr/local/etc/php-fpm.conf && \
    echo '' >> /usr/local/etc/php-fpm.conf && \
    echo '[www]' >> /usr/local/etc/php-fpm.conf && \
    echo 'user = nobody' >> /usr/local/etc/php-fpm.conf && \
    echo 'group = nobody' >> /usr/local/etc/php-fpm.conf && \
    echo 'listen = 127.0.0.1:9000' >> /usr/local/etc/php-fpm.conf && \
    echo 'pm = dynamic' >> /usr/local/etc/php-fpm.conf && \
    echo 'pm.max_children = 5' >> /usr/local/etc/php-fpm.conf && \
    echo 'pm.start_servers = 2' >> /usr/local/etc/php-fpm.conf && \
    echo 'pm.min_spare_servers = 1' >> /usr/local/etc/php-fpm.conf && \
    echo 'pm.max_spare_servers = 3' >> /usr/local/etc/php-fpm.conf

# Copiar script de inicio
COPY start.sh /start.sh
RUN chmod +x /start.sh

# Permisos
RUN chown -R nobody:nobody /var/www/html && \
    chmod -R 755 /var/www/html

CMD ["/start.sh"]
