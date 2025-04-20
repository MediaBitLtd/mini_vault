FROM php:8.4

#Work directory
WORKDIR /var/www

# Install required packages
RUN apt update && apt install -y \
  git \
  zip \
  curl \
  build-essential \
  libzip-dev \
  libxml2-dev \
  libmcrypt-dev \
  libonig-dev

RUN pecl install -o -f redis
RUN docker-php-ext-enable redis


RUN docker-php-ext-install intl
RUN docker-php-ext-install bcmath
RUN docker-php-ext-install zip
RUN docker-php-ext-install mbstring
RUN docker-php-ext-install pdo_mysql

# Install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin/ --filename=composer

# Install nodejs for compiling assets
RUN curl -sL https://deb.nodesource.com/setup_22.x | bash -

RUN apt install -y nodejs

# Clean package manager
RUN apt autoremove -y && apt clean && rm -rf /var/lib/apt/lists/*

# Memory limit
RUN echo 'memory_limit = 512M' >> /usr/local/etc/php/conf.d/docker-php-ram-limit.ini

COPY . /var/www

COPY ./docker/entrypoint.sh /usr/local/bin/entrypoint.sh

COPY ./docker/.env.file .env

RUN groupadd -g 1000 www
RUN useradd -u 1000 -ms /bin/bash -g www www

RUN chown -R www:www /var/www

USER www

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]

CMD ["php artisan serve --host 0.0.0.0"]
