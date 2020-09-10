FROM php:7.4-apache
RUN a2enmod rewrite
RUN apt-get update
RUN docker-php-ext-install pdo pdo_mysql
COPY . /var/www/html
ADD ./000-default.conf /etc/apache2/sites-available

# Install PHP dependencies
RUN apt-get update && apt-get install -y \
    build-essential \
    libpng-dev \
    libpq-dev \
    libonig-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    libmcrypt-dev \
    libpng-dev \
    libwebp-dev \
    zlib1g-dev \
    libxml2-dev \
    libzip-dev \
    libonig-dev \
    graphviz \
    locales \
    zip \
    jpegoptim optipng pngquant gifsicle \
    vim \
    unzip \
    git \
    git \
    curl \
    libcurl4 \
    libcurl4-openssl-dev \
    nginx

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*
# mcrypt
RUN pecl install mcrypt-1.0.3
RUN docker-php-ext-enable mcrypt
# Install extensions
RUN docker-php-ext-configure gd --enable-gd --with-freetype --with-jpeg --with-webp
RUN docker-php-ext-install -j$(nproc) gd
RUN docker-php-ext-install mbstring
RUN docker-php-ext-install zip
RUN docker-php-ext-install exif
RUN docker-php-ext-install pcntl
RUN docker-php-ext-install -j$(nproc) intl
