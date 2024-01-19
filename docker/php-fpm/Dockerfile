FROM php:8.3-fpm

RUN apt update
RUN apt upgrade
RUN apt install -y cron
RUN curl -1sLf 'https://dl.cloudsmith.io/public/symfony/stable/setup.deb.sh' | bash
RUN apt install symfony-cli -y
RUN apt install git -y
RUN apt install unzip -y
RUN (crontab -l ; echo "0 9,18 * * * /usr/local/bin/php /app/bin/console app:get-books") | crontab

WORKDIR /app
