# Learn more about the Server Side Up PHP Docker Images at:
# https://serversideup.net/open-source/docker-php/

FROM serversideup/php:beta-8.3-fpm-nginx as base

RUN apt-get update && apt-get install -y \
    libicu-dev \
    libonig-dev \
    && docker-php-ext-configure intl \
    && docker-php-ext-install intl \
    && docker-php-ext-install mbstring
RUN docker-php-ext-enable intl mbstring

FROM base as development

# Fix permission issues in development by setting the "www-data"
# user to the same user and group that is running docker.
ARG USER_ID
ARG GROUP_ID
RUN docker-php-serversideup-set-id www-data ${USER_ID} ${GROUP_ID}

FROM base as deploy
COPY --chown=www-data:www-data . /var/www/html
