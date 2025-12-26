FROM php:8.1-fpm

# Instalar dependencias del sistema y Nginx
RUN apt-get update && apt-get install -y \
    nginx \
    procps \
    && rm -rf /var/lib/apt/lists/*

# Instalar extensiones de PHP
RUN docker-php-ext-install pdo pdo_mysql

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Crear directorio de trabajo
WORKDIR /var/www/html

# Copiar archivos
COPY . /var/www/html/

# Instalar dependencias de Composer
RUN composer install --no-dev --optimize-autoloader

# Configurar Nginx con fastcgi_pass correcto
RUN echo 'server {\n\
    listen 80;\n\
    server_name _;\n\
    root /var/www/html/public;\n\
    index index.php index.html;\n\
\n\
    access_log /var/log/nginx/access.log;\n\
    error_log /var/log/nginx/error.log debug;\n\
\n\
    location / {\n\
        try_files $uri $uri/ /index.php?$query_string;\n\
    }\n\
\n\
    location ~ \.php$ {\n\
        include fastcgi_params;\n\
        fastcgi_pass unix:/var/run/php-fpm.sock;\n\
        fastcgi_index index.php;\n\
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;\n\
        fastcgi_param PATH_INFO $fastcgi_path_info;\n\
    }\n\
\n\
    location ~ /\.ht {\n\
        deny all;\n\
    }\n\
}' > /etc/nginx/sites-available/default

# Configurar PHP-FPM para usar socket unix
RUN echo '[www]\n\
user = www-data\n\
group = www-data\n\
listen = /var/run/php-fpm.sock\n\
listen.owner = www-data\n\
listen.group = www-data\n\
listen.mode = 0660\n\
pm = dynamic\n\
pm.max_children = 5\n\
pm.start_servers = 2\n\
pm.min_spare_servers = 1\n\
pm.max_spare_servers = 3' > /usr/local/etc/php-fpm.d/www.conf

# Permisos
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Script de inicio mejorado
RUN echo '#!/bin/sh\n\
set -e\n\
echo "Starting PHP-FPM..."\n\
php-fpm -D\n\
sleep 3\n\
echo "PHP-FPM started"\n\
ls -la /var/run/php-fpm.sock || echo "Socket not found yet"\n\
echo "Starting Nginx..."\n\
nginx -g "daemon off;"' > /start.sh && chmod +x /start.sh

EXPOSE 80

CMD ["/start.sh"]