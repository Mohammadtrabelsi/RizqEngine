FROM php:8.2

WORKDIR /app

# System libraries required by the PHP extensions and wkhtmltopdf-based PDF/
# barcode generation.
RUN apt-get update && apt-get install -y \
  libzip-dev \
  libpng-dev \
  libjpeg-dev \
  libfreetype6-dev \
  libxrender1 \
  libxext6 \
  libfontconfig1 \
  zip \
  git \
  curl \
  && apt-get clean && rm -rf /var/lib/apt/lists/*

# Install php extensions (gd + exif are needed by medialibrary/barcode).
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
  && docker-php-ext-install pdo pdo_mysql mysqli gd exif bcmath zip

# Install composer
RUN curl -sS https://getcomposer.org/installer | php -- \
  --install-dir=/usr/bin --filename=composer

COPY . /app

# Platform requirements are now satisfied by the base image, so install
# strictly (no --ignore-platform-reqs) to catch real dependency mismatches.
RUN composer install --no-interaction --prefer-dist --optimize-autoloader

# Give execute permission to startup script
RUN chmod +x /app/docker-startup.sh

ENTRYPOINT [ "/app/docker-startup.sh" ]

