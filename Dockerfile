FROM php:8.4-fpm

# Instalar Nginx y dependencias
RUN apt-get update && apt-get install -y \
    nginx \
    && rm -rf /var/lib/apt/lists/*

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

# Configurar Nginx
RUN rm /etc/nginx/sites-enabled/default
RUN echo 'server {\n\
    listen 80;\n\
    server_name _;\n\
    root /var/www/html/public;\n\
    index index.php index.html;\n\
\n\
    location / {\n\
        try_files $uri $uri/ /index.php?$query_string;\n\
    }\n\
\n\
    location ~ \.php$ {\n\
        include fastcgi_params;\n\
        fastcgi_pass unix:/run/php/php-fpm.sock;\n\
        fastcgi_index index.php;\n\
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;\n\
    }\n\
\n\
    location ~ /\. {\n\
        deny all;\n\
    }\n\
}' > /etc/nginx/sites-enabled/default

# Configurar PHP-FPM para usar socket unix
RUN mkdir -p /run/php && \
    echo '[www]\n\
user = www-data\n\
group = www-data\n\
listen = /run/php/php-fpm.sock\n\
listen.owner = www-data\n\
listen.group = www-data\n\
listen.mode = 0660\n\
pm = dynamic\n\
pm.max_children = 10\n\
pm.start_servers = 2\n\
pm.min_spare_servers = 1\n\
pm.max_spare_servers = 3' > /usr/local/etc/php-fpm.d/www.conf

# Permisos
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Script de inicio
RUN echo '#!/bin/sh\n\
set -e\n\
echo "Starting PHP-FPM..."\n\
php-fpm -D\n\
echo "Waiting for socket..."\n\
sleep 3\n\
echo "Starting Nginx..."\n\
nginx -g "daemon off;"' > /start.sh && chmod +x /start.sh

EXPOSE 80

CMD ["/start.sh"]
