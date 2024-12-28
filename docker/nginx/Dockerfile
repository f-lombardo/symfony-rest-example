FROM debian:stable

ARG PHP_FPM_PORT=9000
ARG UID=1000
ARG PHP_UPSTREAM

RUN apt-get update && apt-get install -y nginx

ADD nginx.conf /etc/nginx/
ADD symfony.conf /etc/nginx/sites-available/

RUN ln -s /etc/nginx/sites-available/symfony.conf /etc/nginx/sites-enabled/symfony \
&& rm /etc/nginx/sites-enabled/default

RUN echo "upstream php-upstream { server ${PHP_UPSTREAM}:${PHP_FPM_PORT}; }" > /etc/nginx/conf.d/upstream.conf

RUN usermod -u ${UID} www-data

CMD ["nginx"]

EXPOSE 80