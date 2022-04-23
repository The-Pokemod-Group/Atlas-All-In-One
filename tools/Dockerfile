FROM php:7.2.2-apache

RUN rm -rf /var/www/html/*
WORKDIR /var/www/html/
COPY ./css/* /var/www/html/css/
COPY ./other/* /var/www/html/other/
COPY ./index.php /var/www/html/
COPY ./config/config.env.php /var/www/html/config/config.php

RUN docker-php-ext-install pdo pdo_mysql
