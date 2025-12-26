FROM php:8.4-fpm-alpine

# Instalar Caddy y bash
RUN apk add --no-cache caddy bash

# Instalar extensiones de PHP
RUN docker-php-ext-install pdo pdo_mysql

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Directorio de trabajo
WORKDIR /var/www/html

# Copiar archivos
COPY . /var/www/html/

# Instalar dependencias
RUN composer install --no-dev --optimize-autoloader

# Configurar PHP-FPM para escuchar en TCP
RUN echo '[www]' > /usr/local/etc/php-fpm.d/www.conf && \
    echo 'listen = 9000' >> /usr/local/etc/php-fpm.d/www.conf && \
    echo 'pm = dynamic' >> /usr/local/etc/php-fpm.d/www.conf && \
    echo 'pm.max_children = 10' >> /usr/local/etc/php-fpm.d/www.conf && \
    echo 'pm.start_servers = 3' >> /usr/local/etc/php-fpm.d/www.conf && \
    echo 'pm.min_spare_servers = 2' >> /usr/local/etc/php-fpm.d/www.conf && \
    echo 'pm.max_spare_servers = 5' >> /usr/local/etc/php-fpm.d/www.conf

# Crear Caddyfile
RUN echo ':80 {' > /etc/caddy/Caddyfile && \
    echo '    root * /var/www/html/public' >> /etc/caddy/Caddyfile && \
    echo '    php_fastcgi 127.0.0.1:9000' >> /etc/caddy/Caddyfile && \
    echo '    file_server' >> /etc/caddy/Caddyfile && \
    echo '    encode gzip' >> /etc/caddy/Caddyfile && \
    echo '}' >> /etc/caddy/Caddyfile

# Permisos
RUN chmod -R 755 /var/www/html

# Crear script de inicio
RUN echo '#!/bin/bash' > /start.sh && \
    echo 'set -e' >> /start.sh && \
    echo 'echo "=== Starting PHP-FPM ==="' >> /start.sh && \
    echo 'php-fpm -F &' >> /start.sh && \
    echo 'sleep 3' >> /start.sh && \
    echo 'echo "=== PHP-FPM started ==="' >> /start.sh && \
    echo 'ps aux | grep php-fpm' >> /start.sh && \
    echo 'echo "=== Starting Caddy ==="' >> /start.sh && \
    echo 'caddy run --config /etc/caddy/Caddyfile' >> /start.sh && \
    chmod +x /start.sh

EXPOSE 80

CMD ["/start.sh"]