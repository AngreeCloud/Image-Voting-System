# Dockerfile para Laravel - Deploy no Render
FROM php:8.2-fpm-alpine

# Instalar dependências do sistema
RUN apk add --no-cache \
    nginx \
    supervisor \
    nodejs \
    npm \
    mysql-client \
    postgresql-dev \
    libzip-dev \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    oniguruma-dev \
    git \
    zip \
    unzip

# Instalar extensões PHP necessárias
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        pdo \
        pdo_mysql \
        pdo_pgsql \
        zip \
        gd \
        mbstring \
        bcmath \
        opcache

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Configurar diretório de trabalho
WORKDIR /var/www/html

# Copiar arquivos do projeto
COPY . .

# Instalar dependências PHP
RUN composer install --optimize-autoloader --no-dev

# Instalar dependências Node e compilar assets
RUN npm install && npm run build

# Configurar permissões
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage \
    && chmod -R 755 /var/www/html/bootstrap/cache

# Criar diretório de uploads
RUN mkdir -p /var/www/html/public/uploads \
    && chown -R www-data:www-data /var/www/html/public/uploads \
    && chmod -R 755 /var/www/html/public/uploads

# Copiar configuração do Nginx
COPY docker/nginx.conf /etc/nginx/nginx.conf
COPY docker/default.conf /etc/nginx/http.d/default.conf

# Copiar configuração do Supervisor
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Copiar script de inicialização
COPY docker/start.sh /usr/local/bin/start.sh
RUN chmod +x /usr/local/bin/start.sh

# Otimizar PHP para produção
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

# Expor porta
EXPOSE 8080

# Comando de inicialização
CMD ["/usr/local/bin/start.sh"]
