# FROM php:8.2-apache
# COPY . /var/www/html/
# RUN a2enmod rewrite
# EXPOSE 80

# Use official PHP image with Apache
FROM php:8.2-apache

# Enable any PHP extensions you need, e.g. mysqli or pdo_mysql for database
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Copy your app code to Apache's document root
COPY . /var/www/html/

# Expose port 80
EXPOSE 80
