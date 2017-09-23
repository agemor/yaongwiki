FROM phpdockerio/php71-fpm:latest

# Install selected extensions and other stuff
RUN apt-get update && apt-get -y install \
        php7.1-mysql \
        php-mbstring \
    && apt-get clean; rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /usr/share/doc/*

WORKDIR "/application"