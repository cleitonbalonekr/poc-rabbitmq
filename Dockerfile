# Use the official PHP image as the base image
FROM php:latest

# Install required dependencies (e.g., Git for Composer)
RUN apt-get update \
  && apt-get install -y git libpq-dev libzip-dev \
  && docker-php-ext-install sockets zip \
  && rm -rf /var/lib/apt/lists/*

# Set the working directory inside the container
WORKDIR /var/www/html

# Copy the PHP application files into the container
COPY ./pep .

# Install dependencies using Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN composer install

# Expose port 80 for the PHP web server
EXPOSE 80

# Command to start the PHP web server
CMD ["php", "-S", "0.0.0.0:80"]
