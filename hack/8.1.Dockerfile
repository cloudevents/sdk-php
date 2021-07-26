FROM php:8.1.0beta1-alpine

LABEL org.opencontainers.image.url="https://github.com/cloudevents/sdk-php/tree/master/hack/8.1.Dockerfile" \
      org.opencontainers.image.documentation="https://github.com/cloudevents/sdk-php/tree/master/hack/README.md" \
      org.opencontainers.image.source="https://github.com/cloudevents/sdk-php" \
      org.opencontainers.image.vendor="CloudEvent" \
      org.opencontainers.image.title="PHP 8.1" \
      org.opencontainers.image.description="PHP 8.1 test environment for cloudevents/sdk-php"

COPY --chown=www-data:www-data install-composer /usr/local/bin/install-composer
RUN chmod +x /usr/local/bin/install-composer \
    && /usr/local/bin/install-composer \
    && rm /usr/local/bin/install-composer

RUN apk update \
    && apk --no-cache upgrade \
    && apk add --no-cache bash ca-certificates git libzip-dev \
    && rm -rf /var/www/html /tmp/pear \
    && chown -R www-data:www-data /var/www

WORKDIR /var/www
ENTRYPOINT ["/var/www/vendor/bin/phpunit"]
