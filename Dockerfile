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

# Configurar PHP-FPM para escuchar en 0.0.0.0:9000
RUN echo '[global]' > /usr/local/etc/php-fpm.conf && \
    echo 'error_log = /proc/self/fd/2' >> /usr/local/etc/php-fpm.conf && \
    echo 'daemonize = no' >> /usr/local/etc/php-fpm.conf && \
    echo '' >> /usr/local/etc/php-fpm.conf && \
    echo '[www]' >> /usr/local/etc/php-fpm.conf && \
    echo 'listen = 0.0.0.0:9000' >> /usr/local/etc/php-fpm.conf && \
    echo 'pm = dynamic' >> /usr/local/etc/php-fpm.conf && \
    echo 'pm.max_children = 5' >> /usr/local/etc/php-fpm.conf && \
    echo 'pm.start_servers = 2' >> /usr/local/etc/php-fpm.conf && \
    echo 'pm.min_spare_servers = 1' >> /usr/local/etc/php-fpm.conf && \
    echo 'pm.max_spare_servers = 3' >> /usr/local/etc/php-fpm.conf && \
    echo 'catch_workers_output = yes' >> /usr/local/etc/php-fpm.conf && \
    echo 'access.log = /proc/self/fd/2' >> /usr/local/etc/php-fpm.conf

# Configurar Nginx
RUN cat > /etc/nginx/http.d/default.conf << 'NGINX_EOF'
server {
    listen 80;
    root /var/www/html/public;
    index index.php;
    
    access_log /dev/stdout;
    error_log /dev/stderr warn;
    
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }
    
    location ~ \.php$ {
        try_files $uri =404;
        fastcgi_pass 127.0.0.1:9000;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
NGINX_EOF

# Configurar Supervisor
RUN cat > /etc/supervisord.conf << 'SUPER_EOF'
[supervisord]
nodaemon=true
user=root
logfile=/dev/null
logfile_maxbytes=0
loglevel=info

[program:php-fpm]
command=php-fpm -F
autostart=true
autorestart=true
stdout_logfile=/dev/stdout
stdout_logfile_maxbytes=0
stderr_logfile=/dev/stderr
stderr_logfile_maxbytes=0
priority=1

[program:nginx]
command=nginx -g "daemon off;"
autostart=true
autorestart=true
stdout_logfile=/dev/stdout
stdout_logfile_maxbytes=0
stderr_logfile=/dev/stderr
stderr_logfile_maxbytes=0
priority=2
SUPER_EOF

# Permisos
RUN chown -R nobody:nobody /var/www/html && \
    chmod -R 755 /var/www/html

EXPOSE 80

CMD ["/usr/bin/supervisord", "-c", "/etc/supervisord.conf"]
