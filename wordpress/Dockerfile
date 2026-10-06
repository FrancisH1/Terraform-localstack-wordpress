FROM wordpress:latest

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        git \
        unzip \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

COPY composer.json /tmp/composer.json

RUN cd /tmp \
    && composer install --no-dev --prefer-dist --no-interaction \
    && mkdir -p /usr/src/wordpress/vendor \
    && cp -r vendor/* /usr/src/wordpress/vendor/

COPY wp-content/plugins/localstack-s3 \
     /usr/src/wordpress/wp-content/plugins/localstack-s3
