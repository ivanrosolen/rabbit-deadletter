FROM php:7.2-fpm

WORKDIR /var/www/html

RUN apt-get update && \
    apt-get install -y \
    unzip \
    git \
    wget

RUN docker-php-ext-install bcmath sockets

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN chmod +x /usr/local/bin/composer

COPY . /var/www/html
RUN composer install --prefer-dist -o
