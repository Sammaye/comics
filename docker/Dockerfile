FROM ubuntu:noble

# Only for build else might confuse others who run apt-get
ARG DEBIAN_FRONTEND=noninteractive
ENV TZ=Europe
ENV ENVIRONMENT=development;
ENV PHP_VERSION=7.4
ENV NODE_VERSION=10.x

# This can still generate noise, I have investigated the reason and some say a hack works but many
# don't, as such I have avoided adding even more vars and layers and just let it output to console
RUN apt-get -qq update \
    && apt-get -qq install software-properties-common \
    && apt-get -qq update \
    && apt-get -qq install zip pkg-config libssl-dev wget cron

# OAuth has been built in a way that causes php version folders to be created under
# /etc/php for 5.6, 7.0, 7.1, 7.2 and 7.3. It does not install these versions, instead
# just makes a file for the plugin within the mods-available folder in each version
RUN add-apt-repository ppa:ondrej/php \
    && apt-get -qq update \
    && apt-get --yes install php${PHP_VERSION}-fpm \
    php${PHP_VERSION}-curl \
    php${PHP_VERSION}-gd \
    php${PHP_VERSION}-zip \
    php${PHP_VERSION}-mysql \
    php${PHP_VERSION}-mbstring \
    php${PHP_VERSION}-xml \
    php${PHP_VERSION}-soap \
    php${PHP_VERSION}-intl \
    php${PHP_VERSION}-oauth \
    php${PHP_VERSION}-dev \
    && mkdir -p /run/php \
    && apt-get --yes install php-pear

RUN pecl install xdebug \
    pecl install mongodb

RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    && php composer-setup.php --install-dir=/usr/local/bin --filename=composer \
    && php -r "unlink('composer-setup.php');"

RUN apt-get --yes install build-essential \
    && curl -sL https://deb.nodesource.com/setup_${NODE_VERSION} | bash - \
    && apt-get --yes install nodejs

RUN apt-get --yes install nginx

# This is to allow us to run mongodump and mongorestore on our php box
RUN wget -qO - https://www.mongodb.org/static/pgp/server-4.4.asc | apt-key add - \
    && echo "deb [ arch=amd64,arm64 ] https://repo.mongodb.org/apt/ubuntu bionic/mongodb-org/4.4 multiverse" | tee /etc/apt/sources.list.d/mongodb-org-4.4.list \
    && apt-get update \
    && apt-get install -y mongodb-database-tools

# Setup cron
COPY ./crontab /etc/cron.d/crontab
RUN chmod 0644 /etc/cron.d/crontab \
    && crontab /etc/cron.d/crontab

# Copying here allows us to reuse cached layers when configuration files change

COPY ./php/${PHP_VERSION}/cli/ /etc/php/${PHP_VERSION}/cli/
COPY ./php/${PHP_VERSION}/fpm/ /etc/php/${PHP_VERSION}/fpm/

COPY ./nginx/ /etc/nginx/
COPY ./nginx/conf.d/ /etc/nginx/conf.d/

COPY ./start.sh /var/
RUN chmod u+x /var/start.sh \
    && /var/start.sh $ENVIRONMENT

COPY ./init.sh /var/
RUN chmod u+x /var/init.sh
CMD /var/init.sh

EXPOSE 80
