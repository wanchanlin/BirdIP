FROM php:8.2-apache

# Optional: enable Apache mod_rewrite if you use .htaccess
RUN a2enmod rewrite

# Copy all website files to Apache root

COPY . /var/www/html

EXPOSE 80