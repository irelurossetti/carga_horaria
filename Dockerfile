###
# Dockerfile for Laravel 12 / PHP 8.2 for Render.com
# Single-file setup: builds PHP vendor & JS assets and runs nginx + php-fpm under supervisord
# - The container will listen on the port defined by the environment variable PORT (Render exposes this)
# - This Dockerfile writes minimal nginx & supervisord configs and an entrypoint script during build
###

############################################################
# 1) Composer stage (install PHP dependencies)
############################################################
FROM composer:2 AS vendor
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --prefer-dist --no-interaction --optimize-autoloader

############################################################
# 2) Node stage (build frontend assets via npm/yarn)
############################################################
FROM node:20-alpine AS assets
WORKDIR /app
COPY package.json package-lock.json ./
RUN npm ci --silent
COPY . .
RUN npm run build --silent || true

############################################################
# 3) Final image
############################################################
FROM php:8.2-fpm-alpine
LABEL maintainer="carga_horaria"

# Install system and PHP build dependencies
RUN apk add --no-cache --virtual .build-deps \
        $PHPIZE_DEPS \
        libzip-dev \
        libpng-dev \
        libjpeg-turbo-dev \
        freetype-dev \
        libxml2-dev \
        oniguruma-dev \
        icu-dev \
    && apk add --no-cache --update \
        nginx \
        supervisor \
        bash \
        tzdata \
        curl \
        gettext \
    && docker-php-ext-configure gd --with-jpeg --with-freetype \
    && docker-php-ext-install -j$(getconf _NPROCESSORS_ONLN) \
        pdo_mysql \
        mbstring \
        exif \
        pcntl \
        bcmath \
        gd \
        zip \
        intl \
    && apk del .build-deps \
    && rm -rf /var/cache/apk/* /tmp/*

# Set working directory
WORKDIR /var/www/html

# Copy app files, composer vendor and built assets from stages
COPY --chown=www-data:www-data . /var/www/html
COPY --from=vendor /app/vendor /var/www/html/vendor
COPY --from=assets /app/public/build /var/www/html/public/build

# Create an nginx config template and supervisors config and entrypoint script
RUN mkdir -p /etc/nginx/conf.d /var/log/nginx /var/log/supervisor \
 && cat > /etc/nginx/conf.d/default.template <<'NGINXCONF'\
server {\
    listen $PORT;\
    server_name _;\
    root /var/www/html/public;\
    index index.php index.html;\
\
    access_log /var/log/nginx/access.log;\
    error_log /var/log/nginx/error.log;\
\
    client_max_body_size 50M;\
\
    add_header X-Frame-Options "SAMEORIGIN";\
    add_header X-Content-Type-Options "nosniff";\
\
    location / {\
        try_files $uri $uri/ /index.php?$query_string;\
    }\
\
    location ~ \\.php$ {\
        include fastcgi_params;\
        fastcgi_pass 127.0.0.1:9000;\
        fastcgi_index index.php;\
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;\
        fastcgi_param PATH_INFO $fastcgi_path_info;\
    }\
\
    location ~ /\\.ht {\
        deny all;\
    }\
}\
NGINXCONF

RUN cat > /etc/supervisord.conf <<'SUPCONF'\
[supervisord]\
nodaemon=true\
logfile=/var/log/supervisord.log\
loglevel=info\
\
[program:php-fpm]\
command=/usr/local/sbin/php-fpm --nodaemonize\
autostart=true\
autorestart=true\
stdout_logfile=/var/log/php-fpm.log\
stderr_logfile=/var/log/php-fpm-err.log\
\
[program:nginx]\
command=/usr/sbin/nginx -g "daemon off;"\
autostart=true\
autorestart=true\
stdout_logfile=/var/log/nginx/access.log\
stderr_logfile=/var/log/nginx/error.log\
SUPCONF

RUN cat > /usr/local/bin/docker-entrypoint.sh <<'ENTRY'\
#!/bin/sh\
set -e\
\
# Setup nginx conf from template using environment PORT\
: "${PORT:=80}"\
if [ -f /etc/nginx/conf.d/default.template ]; then\
    envsubst '$PORT' < /etc/nginx/conf.d/default.template > /etc/nginx/conf.d/default.conf\
fi\
\
# Ensure permissions\
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache || true\
\
# If APP_KEY is not set, show a warning (KEY should be provided via env on Render)\
if [ -z "${APP_KEY}" ]; then\
    echo 'WARNING: APP_KEY not set. Use environment variable APP_KEY to set it.'\
fi\
\
# Run supervisor (which runs php-fpm + nginx)\
exec /usr/bin/supervisord -c /etc/supervisord.conf\
ENTRY

RUN chmod +x /usr/local/bin/docker-entrypoint.sh

# Expose port and use entrypoint
EXPOSE 80
ENTRYPOINT ["/usr/local/bin/docker-entrypoint.sh"]

CMD ["/usr/bin/supervisord", "-c", "/etc/supervisord.conf"]
