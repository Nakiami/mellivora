FROM php:7-apache

MAINTAINER Nakiami <contact@greyboxconcepts.com.au>

RUN apt-get update && apt-get install -y \
    git \
    libssl-dev \
    libcurl4-openssl-dev \
    pkg-config

RUN docker-php-ext-install mbstring curl pdo pdo_mysql

COPY . /var/www/mellivora
COPY install/lamp/mellivora.apache.conf /etc/apache2/sites-available/000-default.conf
COPY include/config/config.inc.php.example /var/www/mellivora/include/config/config.inc.php
COPY include/config/db.inc.php.example /var/www/mellivora/include/config/db.inc.php

RUN sed -e "s?'localhost'?'db'?g" --in-place /var/www/mellivora/include/config/db.inc.php
RUN sed -e "s?'root'?'meldbuser'?g" --in-place /var/www/mellivora/include/config/db.inc.php
RUN sed -e "s?''?'password'?g" --in-place /var/www/mellivora/include/config/db.inc.php

RUN curl -sS https://getcomposer.org/installer | php
RUN mv composer.phar /usr/local/bin/composer
RUN composer global require hirak/prestissimo
WORKDIR /var/www/mellivora/
RUN composer install --no-dev --optimize-autoloader

RUN chown -R www-data:www-data /var/www/mellivora
RUN a2enmod rewrite