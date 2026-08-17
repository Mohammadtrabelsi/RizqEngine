FROM php:8.1-fpm

# Build args so the container runs as your host user (avoids root-owned files)
ARG UID=1000
ARG GID=1000

# System dependencies for common Laravel extensions
RUN apt-get update && apt-get install -y --no-install-recommends \
        git \
        curl \
        zip \
        unzip \
        libzip-dev \
        libpng-dev \
        libjpeg-dev \
        libfreetype6-dev \
        libonig-dev \
        libxml2-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j"$(nproc)" \
        pdo_mysql \
        mysqli \
        mbstring \
        zip \
        exif \
        pcntl \
        bcmath \
        gd \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Create a non-root user matching the host UID/GID
RUN groupadd -g "${GID}" appuser \
    && useradd -u "${UID}" -g appuser -m appuser

WORKDIR /var/www/html

USER appuser

EXPOSE 9000
CMD ["php-fpm"]
