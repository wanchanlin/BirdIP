FROM php:8.2-apache

# Optional: enable Apache mod_rewrite if you use .htaccess
RUN a2enmod rewrite

# Copy all website files to Apache root

COPY . /index.php
COPY . /bird.php

EXPOSE 80