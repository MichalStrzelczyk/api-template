FROM mileschou/phalcon:7.4-fpm-alpine
COPY --from=composer:1.9.1 /usr/bin/composer /usr/bin/composer
RUN apk add git &&\
    apk add unzip
