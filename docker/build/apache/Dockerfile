FROM php:7.1-apache

RUN a2enmod ssl
RUN a2enmod rewrite

## Basic tools
RUN apt-get update -y &&\
    apt-get upgrade -y &&\
    apt-get install -y bash \
            curl \
            wget \
            ca-certificates \
            vim \
            git \
            openssl

RUN echo "GREEN='\[\e[1;32m\]'" >> /root/.bashrc &&\
    echo "reset='\[\e[0m\]'" >> /root/.bashrc &&\
    echo 'PS1="${GREEN}(docker)${reset} [\u@\h \W]\\$ "' >> /root/.bashrc

## PHP
RUN apt-get update && apt-get install -y \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libpng12-dev \
        libxrender1 libfontconfig libXext6 \
    && docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ \
    && docker-php-ext-install -j4 pdo_mysql mbstring bcmath zip opcache

RUN printf "\n" | pecl install apcu

## Composer
ENV PATH "/composer/vendor/bin:$PATH"
ENV COMPOSER_ALLOW_SUPERUSER 1
ENV COMPOSER_HOME /composer
ENV COMPOSER_VERSION 1.3.2
ENV COMPOSER_PROCESS_TIMEOUT 2000

RUN curl -s -f -L -o /tmp/installer.php https://raw.githubusercontent.com/composer/getcomposer.org/5fd32f776359b8714e2647ab4cd8a7bed5f3714d/web/installer \
 && php -r " \
    \$signature = '55d6ead61b29c7bdee5cccfb50076874187bd9f21f65d8991d46ec5cc90518f447387fb9f76ebae1fbbacf329e583e30'; \
    \$hash = hash('SHA384', file_get_contents('/tmp/installer.php')); \
    if (!hash_equals(\$signature, \$hash)) { \
        unlink('/tmp/installer.php'); \
        echo 'Integrity check failed, installer is either corrupt or worse.' . PHP_EOL; \
        exit(1); \
    }" \
 && php /tmp/installer.php --no-ansi --install-dir=/usr/bin --filename=composer --version=${COMPOSER_VERSION} \
 && rm /tmp/installer.php \
 && composer --ansi --version --no-interaction

RUN sed -e 's%www-data:x:33%www-data:x:1000%g' /etc/passwd -i


## Local Time
RUN cp /usr/share/zoneinfo/Europe/Paris /etc/localtime


WORKDIR /var/www/app