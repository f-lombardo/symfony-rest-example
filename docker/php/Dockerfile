ARG COMPOSER_VERSION=2
ARG PHP_VERSION

FROM composer:$COMPOSER_VERSION AS composer
FROM php:${PHP_VERSION}-fpm

ARG TIMEZONE

RUN apt-get update && apt-get install -y \
    curl \
    vim \
    openssl \
    git \
    unzip \
    libzip-dev \
    libpq-dev \
    libicu-dev \
    wkhtmltopdf \
    wget

# Install Composer
COPY --from=composer /usr/bin/composer /usr/bin/composer

# Set timezone
RUN ln -snf /usr/share/zoneinfo/${TIMEZONE} /etc/localtime && echo ${TIMEZONE} > /etc/timezone \
&& printf '[PHP]\ndate.timezone = "%s"\n', ${TIMEZONE} > /usr/local/etc/php/conf.d/tzone.ini \
&& "date"

# Type docker-php-ext-install to see available extensions
RUN docker-php-ext-install pdo pdo_pgsql zip intl bcmath

# install xdebug
RUN pecl install xdebug \
&& docker-php-ext-enable xdebug

# Enable opcache
RUN docker-php-ext-enable opcache

COPY php-custom.ini /usr/local/etc/php/conf.d/docker-php-custom.ini
COPY xdebug.ini /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

# install yq
RUN wget https://github.com/mikefarah/yq/releases/latest/download/yq_linux_amd64 -O /usr/bin/yq && chmod +x /usr/bin/yq

WORKDIR /var/www/symfony
