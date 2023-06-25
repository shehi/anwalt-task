FROM php:8.2-fpm
LABEL maintainer="Shahriyar Imanov <shehi@imanov.me>"

WORKDIR /var/www

ENV DEBIAN_FRONTEND noninteractive
ENV TERM xterm

RUN apt-get update -y \
    && apt-get install -y apt-utils \
    && apt-get upgrade -y \
    && mkdir -p /usr/share/man/man1 \
    && apt-get install -y \
        libmagickwand-dev \
        sudo \
        unzip \
        wget

# Create php.ini file
RUN mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"
RUN sed -i -e "s/upload_max_filesize =.*/upload_max_filesize = 100M/g" "$PHP_INI_DIR/php.ini"
RUN sed -i -e "s/post_max_size =.*/post_max_size = 100M/g" "$PHP_INI_DIR/php.ini"

RUN apt-get install -y libzip-dev && docker-php-ext-install zip

RUN pecl install -of imagick \
    && echo "extension=imagick.so" | tee /usr/local/etc/php/conf.d/imagick.ini

RUN apt-get install -y libavif-dev libfreetype6-dev libjpeg-dev libjpeg62-turbo-dev libpng-dev \
    && docker-php-ext-configure gd --with-avif --with-jpeg --with-webp \
    && docker-php-ext-install gd

# Install PECL php-xdebug
RUN pecl install -of xdebug \
    && rm -rf /tmp/pear \
    && echo "zend_extension=xdebug.so" | tee /usr/local/etc/php/conf.d/xdebug.ini

# Port for XDebug
EXPOSE 54321

# Timezone
RUN echo "date.timezone = UTC" | tee /usr/local/etc/php/conf.d/date.ini

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin --filename=composer

# Some checks
RUN php -m

# Clean-up
RUN apt-get clean \
    && apt-get remove -y gnupg \
    && apt-get autoremove -y \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

# Add a non-root user to prevent files being created with root permissions on host machine.
ARG PUID=1000
ARG PGID=1000
RUN groupadd -g $PGID anwalt && useradd -u $PUID -g anwalt -m anwalt
RUN usermod -aG sudo anwalt
RUN sed -i -e "s/%sudo.*/%sudo ALL=(ALL:ALL) NOPASSWD: ALL/g" /etc/sudoers

COPY ./profile /etc/profile
COPY ./profile /etc/bashrc

USER anwalt
