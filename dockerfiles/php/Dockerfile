FROM php:5.6
MAINTAINER Johann Brocail <contact@johannbrocail.fr>

RUN apt-get update && \
 apt-get install -y \
     curl \
     libicu-dev  && \
 rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install intl && \
 docker-php-ext-install bcmath && \
 docker-php-ext-install mbstring && \
 docker-php-ext-install pcntl

ADD https://github.com/alanxz/rabbitmq-c/releases/download/v0.5.2/rabbitmq-c-0.5.2.tar.gz /usr/src/rabbitmq-c.tar.gz
RUN cd /usr/src/ && tar -xvzf /usr/src/rabbitmq-c.tar.gz && cd rabbitmq-c-0.5.2 && ./configure && make && make install && rm -rf /usr/src/rabbitmq*

ADD https://pecl.php.net/get/amqp-1.4.0.tgz /usr/src/php/ext/amqp.tgz
RUN tar -xf /usr/src/php/ext/amqp.tgz -C /usr/src/php/ext/ && \
 rm /usr/src/php/ext/amqp.tgz && \
 docker-php-ext-install amqp-1.4.0

# Configurations
RUN ln -sf /usr/share/zoneinfo/Europe/Paris /etc/localtime

RUN echo "date.timezone = \"Europe/Paris\"" > /usr/local/etc/php/conf.d/timezone.ini

# Set working directory.
WORKDIR /app

# Default command
CMD ["-v"]

# Entrypoint
ENTRYPOINT ["php"]
