# ------------------------------------------------------------
# ✅ Laravel PHP-FPM Dockerfile (Alpine)
# PHP 8.2 + Composer + NodeJS for local builds
# ------------------------------------------------------------

FROM php:8.2-fpm-alpine

# ------------------------------------------------------------
# 1️⃣ System dependencies
# ------------------------------------------------------------
RUN apk add --no-cache \
    bash \
    git \
    unzip \
    curl \
    icu-dev \
    libzip-dev \
    zlib-dev \
    oniguruma-dev \
    libpng-dev \
    jpeg-dev \
    freetype-dev \
    mysql-client \
    nodejs \
    npm \
    shadow

# ------------------------------------------------------------
# 2️⃣ PHP Extensions
# ------------------------------------------------------------
RUN docker-php-ext-install \
    pdo_mysql \
    intl \
    zip \
    bcmath \
    mbstring \
    exif \
    pcntl \
    opcache

# ------------------------------------------------------------
# 3️⃣ Composer
# ------------------------------------------------------------
COPY --from=composer:2.7 /usr/bin/composer /usr/bin/composer

# ------------------------------------------------------------
# 4️⃣ Working Directory
# ------------------------------------------------------------
WORKDIR /var/www/html

# ------------------------------------------------------------
# 5️⃣ UID/GID Mapping for host file permissions
# ------------------------------------------------------------
ARG UID=1000
ARG GID=1000

RUN set -eux; \
    groupmod -g ${GID} www-data; \
    usermod -u ${UID} -g www-data www-data; \
    mkdir -p /var/www/html; \
    chown -R www-data:www-data /var/www/html

USER www-data

# ------------------------------------------------------------
# ✅ NOTES
# ------------------------------------------------------------
# - This image includes Node & NPM for asset building.
# - For production, you can remove Node/NPM and prebuild assets.
# - To install composer deps:
#     docker exec -it water_billing_app composer install
# ------------------------------------------------------------
