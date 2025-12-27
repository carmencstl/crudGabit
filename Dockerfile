FROM php:8.4-fpm-alpine

# Instalar Nginx y supervisor
RUN apk add --no-cache nginx supervisor

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
RUN mkdir -p /run/nginx /run/php

# Script de inicio que configura el puerto dinámico
RUN cat > /start.sh << 'START_EOF'
#!/bin/sh

# Railway proporciona el puerto en $PORT
PORT=${PORT:-8080}

echo "Configurando Nginx para escuchar en puerto $PORT"

# Configurar Nginx con el puerto dinámico
cat > /etc/nginx/http.d/default.conf << NGINX_CONF
server {
    listen ${PORT};
    root /var/www/html/public;
    index index.php;
    
    location / {
        try_files \$uri \$uri/ /index.php?\$query_string;
    }
    
    location ~ \.php\$ {
        try_files \$uri =404;
        fastcgi_pass 127.0.0.1:9000;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME \$document_root\$fastcgi_script_name;
        include fastcgi_params;
    }
}
NGINX_CONF

echo "Iniciando PHP-FPM"
php-fpm -D

echo "Iniciando Nginx en puerto $PORT"
nginx -g "daemon off;"
START_EOF

RUN chmod +x /start.sh

# Configurar PHP-FPM
RUN echo '[global]' > /usr/local/etc/php-fpm.conf && \
    echo 'error_log = /proc/self/fd/2' >> /usr/local/etc/php-fpm.conf && \
    echo 'daemonize = no' >> /usr/local/etc/php-fpm.conf && \
    echo '' >> /usr/local/etc/php-fpm.conf && \
    echo '[www]' >> /usr/local/etc/php-fpm.conf && \
    echo 'listen = 127.0.0.1:9000' >> /usr/local/etc/php-fpm.conf && \
    echo 'pm = dynamic' >> /usr/local/etc/php-fpm.conf && \
    echo 'pm.max_children = 5' >> /usr/local/etc/php-fpm.conf && \
    echo 'pm.start_servers = 2' >> /usr/local/etc/php-fpm.conf && \
    echo 'pm.min_spare_servers = 1' >> /usr/local/etc/php-fpm.conf && \
    echo 'pm.max_spare_servers = 3' >> /usr/local/etc/php-fpm.conf

# Permisos
RUN chown -R nobody:nobody /var/www/html && \
    chmod -R 755 /var/www/html

CMD ["/start.sh"]
