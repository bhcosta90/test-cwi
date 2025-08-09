FROM docker.io/php:8.4.3-apache

ENV COMPOSER_VERSION=2.8.5

# Install Composer
ADD https://getcomposer.org/download/${COMPOSER_VERSION}/composer.phar /usr/local/bin/composer

# Install PHP extensions
ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/install-php-extensions

RUN chmod 775 /usr/local/bin/composer /usr/local/bin/install-php-extensions && \
    apt update && apt install -y zip unzip git && \
    install-php-extensions pdo pdo_mysql mbstring bcmath xml tokenizer json curl gd intl &&  \
    a2enmod rewrite &&  \
    sed -i 's|/var/www/html|/var/www/html/public|g' /etc/apache2/sites-available/000-default.conf

WORKDIR /var/www/html

EXPOSE 80
