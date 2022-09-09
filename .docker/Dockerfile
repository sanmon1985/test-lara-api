FROM ubuntu:20.04

# Обновление и установка пакетов
RUN DEBIAN_FRONTEND=noninteractive apt -qq -y update && \
    DEBIAN_FRONTEND=noninteractive apt -qq -y install \
        curl \
        gnupg2 \
        software-properties-common \
        sudo && \
    LC_ALL=C.UTF-8 add-apt-repository ppa:ondrej/php && \
    LC_ALL=C.UTF-8 add-apt-repository ppa:ondrej/nginx && \
    DEBIAN_FRONTEND=noninteractive apt -qq -y update && \
    DEBIAN_FRONTEND=noninteractive apt -qq -y upgrade && \
    DEBIAN_FRONTEND=noninteractive apt -qq -y install \
        cron \
        git \
        nginx \
        php8.1 \
        php8.1-bcmath \
        php8.1-ctype \
        php8.1-curl \
        php8.1-fpm \
        php8.1-imagick \
        php8.1-intl \
        php8.1-mbstring \
        php8.1-pdo \
        php8.1-pdo-mysql \
        php8.1-pdo-sqlite \
        php8.1-redis \
        php8.1-tokenizer \
        php8.1-xdebug \
        php8.1-xml \
        php8.1-zip \
        supervisor \
        tzdata \
        unzip \
        zip && \
    ln -fs /usr/share/zoneinfo/Europe/Moscow /etc/localtime && \
    dpkg-reconfigure --frontend noninteractive tzdata && \
    DEBIAN_FRONTEND=noninteractive apt -qq -y remove software-properties-common && \
    DEBIAN_FRONTEND=noninteractive apt -qq -y autoremove && \
    rm -rf /var/lib/{apt,dpkg,cache,log}

# Конфигурация php
RUN mkdir /run/php
COPY ./config/php/php.ini /etc/php/8.1/cli/conf.d/99-php.ini
COPY ./config/php/php.ini /etc/php/8.1/fpm/conf.d/99-php.ini

# Конфигурация nginx
COPY ./config/nginx/nginx.conf /etc/nginx/sites-available/testapp-backend.conf
RUN ln -s /etc/nginx/sites-available/testapp-backend.conf /etc/nginx/sites-enabled/

# Конфигурация supervisor
COPY ./config/supervisor/supervisor.conf /etc/supervisor/conf.d/testapp-backend.conf

# Установка composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Перекрашивание основной консоли
RUN sed -i 's/#force_color_prompt=yes/force_color_prompt=yes/g' /root/.bashrc && \
    sed -i 's/01;32m/01;33m/g' /root/.bashrc && \
    sed -i 's/01;34m/01;36m/g' /root/.bashrc

# Настройка дебагера для PHP внутри Docker
ENV PHP_IDE_CONFIG "serverName=api.testapp.local:10080"

# Инструкции запуска
WORKDIR /app
EXPOSE 443
CMD ["supervisord", "-n", "-c", "/etc/supervisor/supervisord.conf"]

#Если надо запустить контейнер без команды
#CMD ["tail", "-f", "/dev/null"]
