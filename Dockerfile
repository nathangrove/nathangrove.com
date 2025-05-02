# Use the official PHP Apache image as a base
FROM php:8.2-apache

# Enable Apache's rewrite module
RUN a2enmod rewrite

# Apache runs as www-data by default, ensure permissions are okay if needed
# (Usually not necessary if mounting from host with appropriate user permissions)

# Expose port 80 (standard HTTP port)
EXPOSE 80
