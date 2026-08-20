# Base PHP 8.5 CLI (imagem oficial — inclui sqlite3/pdo_sqlite por padrão)
FROM php:8.5-cli

# Dependências do sistema + extensões PHP necessárias para o Laravel
RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        git \
        unzip \
        libpq-dev \
        libzip-dev \
        libonig-dev \
        $PHPIZE_DEPS \
    && docker-php-ext-install -j"$(nproc)" pdo_pgsql pgsql mbstring zip pcntl sockets \
    && pecl install pcov \
    && docker-php-ext-enable pcov \
    && pecl install xdebug \
    && docker-php-ext-enable xdebug \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Sobe o memory_limit do PHP CLI (padrão 128M estoura no PHPStan nível max e nos workers paralelos)
RUN echo 'memory_limit=1G' > /usr/local/etc/php/conf.d/zz-memory-limit.ini

# Configuração do Xdebug para debugging com o VSCode (ver docker/xdebug.ini e .vscode/launch.json)
COPY docker/xdebug.ini /usr/local/etc/php/conf.d/zz-xdebug.ini

# Composer (phar da imagem oficial — roda em qualquer versão do PHP)
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Bun — gerenciador de pacotes e runtime do frontend do starter kit
RUN curl -fsSL https://bun.sh/install | bash
ENV PATH="/root/.bun/bin:${PATH}"

# Node.js LTS 24 — runtime para o Playwright (testes de browser) e para as
# ferramentas do frontend (vp/vite-plus precisam de type-stripping, Node >= 22.6)
RUN curl -fsSL https://deb.nodesource.com/setup_24.x | bash - \
    && apt-get install -y --no-install-recommends nodejs \
    && rm -rf /var/lib/apt/lists/*

# Dependências de sistema do Chromium (necessárias para os testes de browser do Pest)
# Embutidas na imagem para persistirem entre recriações do container.
RUN bunx playwright@1.62.1 install-deps chromium

WORKDIR /var/www/html

EXPOSE 80

COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
