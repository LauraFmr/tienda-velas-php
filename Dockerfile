FROM php:8.2-apache


# Extensiones necesarias
RUN apt-get update && apt-get install -y --fix-missing \
libpq-dev \
libpng-dev \
libonig-dev \
libxml2-dev \
zip \
unzip \
git \
&& docker-php-ext-install pdo pdo_pgsql mbstring exif pcntl bcmath gd \
&& rm -rf /var/lib/apt/lists/*


# Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer


# DocumentRoot → src/
ENV APACHE_DOCUMENT_ROOT /var/www/html/src
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
-e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf


# Habilitar mod_rewrite
RUN a2enmod rewrite


WORKDIR /var/www/html