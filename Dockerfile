FROM git.skill17.com/images/php-apache

COPY . /var/www/html/ 
COPY .env /var/www/html/.env

RUN rm -r .git
RUN rm Dockerfile

RUN /usr/local/bin/composer install

RUN chown -R www-data.www-data *