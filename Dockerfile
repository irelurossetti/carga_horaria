# ---------------------------
# STAGE 1 : Build Frontend 
# ---------------------------
FROM node:20 AS vite-builder

WORKDIR /app

COPY package.json package-lock.json ./
RUN npm ci

COPY . .
RUN npm run build

# ---------------------------
# STAGE 2 : PHP + Composer
# ---------------------------
FROM php:8.3-fpm AS php-builder

# Instalar dependencias necesarias para GD, ZIP y PostgreSQL
RUN apt-get update && apt-get install -y \
    git curl zip unzip libzip-dev libpq-dev \
    libpng-dev libjpeg62-turbo-dev libfreetype6-dev \
    libonig-dev libxml2-dev supervisor nginx && \
    docker-php-ext-configure gd --with-freetype --with-jpeg && \
    docker-php-ext-install gd zip pdo pdo_pgsql && \
    docker-php-ext-enable pdo_pgsql && \
    rm -rf /var/lib/apt/lists/*

# Instalar Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader

COPY . .

# Copiar build de Vite desde la etapa 1
COPY --from=vite-builder /app/public/build /var/www/html/public/build

# Ajustar permisos
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# ---------------------------
# STAGE 3 : Final (Nginx + PHP-FPM)
# ---------------------------
FROM php:8.3-fpm

# Instalar solo dependencias necesarias para runtime
RUN apt-get update && apt-get install -y \
    libpq-dev libzip-dev libpng-dev libjpeg62-turbo-dev libfreetype6-dev \
    supervisor nginx && \
    docker-php-ext-configure gd --with-freetype --with-jpeg && \
    docker-php-ext-install gd zip pdo pdo_pgsql && \
    rm -rf /var/lib/apt/lists/*

WORKDIR /var/www/html

# Copiar proyecto ya construido
COPY --from=php-builder /var/www/html /var/www/html

# Configuración de Nginx y Supervisor
COPY ./docker/nginx.conf /etc/nginx/sites-available/default
COPY ./docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

EXPOSE 80

CMD ["/usr/bin/supervisord", "-n"]
