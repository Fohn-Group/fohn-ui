###################################################
## # build command:                              ##
## docker build -f Dockerfile .. -t fohn-ui-test ##
## # run command:                                ##
## docker run --rm -p 80:80 -it fohn-ui-test
## Open browser to http://localhost/app-test
###################################################

FROM php:apache

RUN apt-get update && apt-get install -y \
        libicu-dev git jq unzip npm \
    && docker-php-ext-configure intl \
    && docker-php-ext-install intl \
    && docker-php-ext-install pdo pdo_mysql

WORKDIR /var/www/html/

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer


ADD composer.json .
RUN jq 'del(."require-release")|del(."require-dev")' < composer.json > tmp && mv tmp composer.json \
    && composer require --no-update fzaninotto/faker:^1.6 \
    && composer install --no-dev


COPY public public
COPY src src
COPY template template
COPY app-test app-test

RUN php app-test/_app-data/create-sqlite-db.php
