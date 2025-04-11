FROM php:8.3-fpm

COPY php.ini /usr/local/etc/php/

RUN apt update \
  && apt install -y default-mysql-client zlib1g-dev libzip-dev unzip libpng-dev libjpeg-dev libfreetype6-dev \
  && docker-php-ext-configure gd --with-freetype --with-jpeg \
  && docker-php-ext-install pdo_mysql zip gd


RUN curl -sS https://getcomposer.org/installer | php \
  && mv composer.phar /usr/local/bin/composer \
  && composer self-update

  # msmtp の設定を追加
RUN echo "defaults" > /etc/msmtprc \
  && echo "account default" >> /etc/msmtprc \
  && echo "host mailhog" >> /etc/msmtprc \
  && echo "port 1025" >> /etc/msmtprc \
  && echo "auto_from on" >> /etc/msmtprc \
  && echo "tls off" >> /etc/msmtprc \
  && echo "logfile /var/log/msmtp.log" >> /etc/msmtprc \
  && chmod 600 /etc/msmtprc

# PHP の sendmail_path を msmtp に設定
RUN echo "sendmail_path = /usr/bin/msmtp -t" > /usr/local/etc/php/conf.d/mailhog.ini


WORKDIR /var/www