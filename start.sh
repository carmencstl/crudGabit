#!/bin/sh
set -e

PORT=${PORT:-8080}

echo "=== Starting application on port $PORT ==="

# Configurar Nginx
cat > /etc/nginx/http.d/default.conf <<NGINX
server {
    listen $PORT;
    server_name _;
    root /var/www/html/public;
    index index.php index.html;
    
    # Logs
    access_log /dev/stdout;
    error_log /dev/stderr warn;
    
    # Servir archivos estáticos directamente
    location ~* \.(css|js|jpg|jpeg|png|gif|ico|svg|woff|woff2|ttf|eot)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
        try_files \$uri =404;
    }
    
    # Intentar servir archivos estáticos primero, luego PHP
    location / {
        try_files \$uri \$uri/ /index.php?\$query_string;
    }
    
    # Procesar archivos PHP
    location ~ \.php$ {
        try_files \$uri =404;
        fastcgi_split_path_info ^(.+\.php)(/.+)$;
        fastcgi_pass 127.0.0.1:9000;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME \$document_root\$fastcgi_script_name;
        include fastcgi_params;
    }
}
NGINX

echo "=== Starting PHP-FPM ==="
php-fpm -D

sleep 2

echo "=== Starting Nginx on port $PORT ==="
exec nginx -g "daemon off;"
