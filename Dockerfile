FROM php:8.1-cli

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

# Permisos
RUN chmod -R 755 /var/www/html

# Exponer puerto
EXPOSE 8080

# Usar servidor PHP integrado con router personalizado
CMD ["php", "-S", "0.0.0.0:8080", "-t", "public"]
