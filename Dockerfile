FROM php:8.1-apache

# Install MySQL support
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Copy your app into container
COPY ./app /var/www/html/