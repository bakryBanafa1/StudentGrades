FROM php:8.2-apache

# Install mysqli extension
RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli

# Enable Apache rewrite module
RUN a2enmod rewrite

# Change document root to /app
ENV APACHE_DOCUMENT_ROOT /app
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Set workdir
WORKDIR /app

# Copy application files
COPY . /app

# Set permissions for the whole app directory
RUN chown -R www-data:www-data /app && chmod -R 775 /app

# Expose port 80
EXPOSE 80
