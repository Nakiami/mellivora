FROM php:7-apache

MAINTAINER Nakiami <contact@greyboxconcepts.com.au>

RUN apt-get update && apt-get install -y \
    git \
    libssl-dev \
    libcurl4-openssl-dev \
    pkg-config \
    zip \
    unzip \
    libonig-dev

RUN docker-php-ext-install mbstring curl pdo pdo_mysql

COPY . /var/www/mellivora
COPY install/lamp/mellivora.apache.conf /etc/apache2/sites-available/000-default.conf

ENV COMPOSER_ALLOW_SUPERUSER=1
RUN curl -sS https://getcomposer.org/installer | php
RUN mv composer.phar /usr/local/bin/composer
WORKDIR /var/www/mellivora/
RUN composer install --no-dev --optimize-autoloader

RUN chown -R www-data:www-data /var/www/mellivora
RUN a2enmod rewrite
