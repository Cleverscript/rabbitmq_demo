FROM php:8.3.9-fpm

RUN apt-get update && apt-get install -y \
    libmcrypt-dev \
    libpq-dev \
    libicu-dev \
    libjson-c-dev \
    libcurl4-openssl-dev \
    libmcrypt-dev \
    git curl wget zip unzip vim htop man procps telnet;

RUN pecl install xdebug \
    && docker-php-ext-enable xdebug;

RUN pecl install redis \
    && docker-php-ext-enable redis;

# Install php extension
RUN docker-php-ext-install pdo pdo_pgsql intl curl bcmath opcache && \
    docker-php-ext-configure opcache --enable-opcache && \
    docker-php-ext-install sockets;

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN docker-php-ext-configure exif
RUN docker-php-ext-install exif
RUN docker-php-ext-enable exif

# копируем кастомные конфиги PHP
COPY ./conf.d/* ${PHP_INI_DIR}/conf.d/


# создаем группу и пользователя под которым будет запускаться PHP
RUN groupadd -g 1000 b24phpsdk
RUN useradd -u 1000 -g b24phpsdk -G sudo -m b24phpsdk
RUN echo 'b24phpsdk:qwerty' | chpasswd
# указываем под каким пользователем запускать PHP
USER b24phpsdk


WORKDIR /var/www/public
