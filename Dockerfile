# ---------------------------
# STAGE 1 : Build Frontend 
# ---------------------------
FROM node:20 AS vite-builder

WORKDIR /app

COPY package.json package-lock.json ./
RUN npm install

COPY . .
RUN npm run build


# ---------------------------
# STAGE 2 : PHP + Composer
# ---------------------------
FROM php:8.3-fpm AS php-builder

# Instalar dependencias del sistema
RUN apt-get update && apt-get install -y \
    git curl zip unzip libpq-dev libpng-dev \
    libonig-dev libxml2-dev supervisor nginx && \
    docker-php-ext-install pdo pdo_pgsql && \
    docker-php-ext-enable pdo_pgsql

# Instalar Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader

COPY . .

# Copiar build de Vite desde la etapa 1
COPY --from=vite-builder /app/public/build /var/www/html/public/build

# Permisos
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache


# ---------------------------
# STAGE 3 : Final RUN (Nginx + PHP-FPM)
# ---------------------------
FROM php:8.3-fpm

RUN apt-get update && apt-get install -y supervisor nginx && \
    rm -rf /var/lib/apt/lists/*

# Config PHP
RUN docker-php-ext-install pdo pdo_pgsql

WORKDIR /var/www/html

# Copiar proyecto ya construido
COPY --from=php-builder /var/www/html /var/www/html

# Copiar config de nginx
COPY ./docker/nginx.conf /etc/nginx/sites-available/default

# Supervisor para ejecutar Nginx + PHP-FPM
COPY ./docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

EXPOSE 80

CMD ["/usr/bin/supervisord", "-n"]
