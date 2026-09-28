FROM php:8.3-cli

# Dependências do sistema para CodeIgniter 4 (intl, zip, gd, mbstring, etc.)
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libicu-dev \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    unzip \
    sqlite3 \
    libsqlite3-dev

# Limpar cache do apt
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Extensões PHP obrigatórias para CI4 e MySQL
RUN docker-php-ext-configure intl && \
    docker-php-ext-install intl mysqli pdo_mysql pdo_sqlite mbstring exif pcntl bcmath gd zip

# Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

EXPOSE 8080

CMD ["php", "spark", "serve", "--host", "0.0.0.0", "--port", "8080"]
