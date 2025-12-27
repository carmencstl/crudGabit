FROM php:8.4-cli-alpine

# Instalar extensiones de PHP necesarias
RUN docker-php-ext-install pdo pdo_mysql

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Directorio de trabajo
WORKDIR /var/www/html

# Copiar archivos del proyecto
COPY . /var/www/html/

# Instalar dependencias de Composer
RUN composer install --no-dev --optimize-autoloader

# Dar permisos correctos
RUN chmod -R 755 /var/www/html

# Exponer el puerto
EXPOSE 8080

# Iniciar servidor PHP con el router que maneja archivos estáticos
CMD ["php", "-S", "0.0.0.0:8080", "-t", "public", "router.php"]
