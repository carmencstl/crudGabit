FROM php:8.4-fpm-alpine

# Instalar Caddy y supervisor
RUN apk add --no-cache caddy supervisor

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

# Configurar PHP-FPM para escuchar en 0.0.0.0:9000
RUN echo '[www]' > /usr/local/etc/php-fpm.d/www.conf && \
    echo 'listen = 0.0.0.0:9000' >> /usr/local/etc/php-fpm.d/www.conf && \
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

# Configurar Supervisor para mantener ambos procesos corriendo
RUN mkdir -p /etc/supervisor.d && \
    echo '[supervisord]' > /etc/supervisor.d/supervisord.ini && \
    echo 'nodaemon=true' >> /etc/supervisor.d/supervisord.ini && \
    echo 'user=root' >> /etc/supervisor.d/supervisord.ini && \
    echo '' >> /etc/supervisor.d/supervisord.ini && \
    echo '[program:php-fpm]' >> /etc/supervisor.d/supervisord.ini && \
    echo 'command=php-fpm -F' >> /etc/supervisor.d/supervisord.ini && \
    echo 'autostart=true' >> /etc/supervisor.d/supervisord.ini && \
    echo 'autorestart=true' >> /etc/supervisor.d/supervisord.ini && \
    echo 'stdout_logfile=/dev/stdout' >> /etc/supervisor.d/supervisord.ini && \
    echo 'stdout_logfile_maxbytes=0' >> /etc/supervisor.d/supervisord.ini && \
    echo 'stderr_logfile=/dev/stderr' >> /etc/supervisor.d/supervisord.ini && \
    echo 'stderr_logfile_maxbytes=0' >> /etc/supervisor.d/supervisord.ini && \
    echo '' >> /etc/supervisor.d/supervisord.ini && \
    echo '[program:caddy]' >> /etc/supervisor.d/supervisord.ini && \
    echo 'command=caddy run --config /etc/caddy/Caddyfile' >> /etc/supervisor.d/supervisord.ini && \
    echo 'autostart=true' >> /etc/supervisor.d/supervisord.ini && \
    echo 'autorestart=true' >> /etc/supervisor.d/supervisord.ini && \
    echo 'stdout_logfile=/dev/stdout' >> /etc/supervisor.d/supervisord.ini && \
    echo 'stdout_logfile_maxbytes=0' >> /etc/supervisor.d/supervisord.ini && \
    echo 'stderr_logfile=/dev/stderr' >> /etc/supervisor.d/supervisord.ini && \
    echo 'stderr_logfile_maxbytes=0' >> /etc/supervisor.d/supervisord.ini

EXPOSE 80

CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor.d/supervisord.ini"]
FROM php:8.4-cli-alpine

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

# Permisos
RUN chmod -R 755 /var/www/html

EXPOSE 8080

# Servidor PHP directo - simple y funciona
CMD ["php", "-S", "0.0.0.0:8080", "-t", "public"]