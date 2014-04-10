#!/bin/bash

apt-get install -y nginx php5-fpm

cp default /etc/nginx/sites-available/tatoeba
rm -f /etc/nginx/sites-available/*
rm -f /etc/nginx/sites-enabled/*
ln -s /etc/nginx/sites-available/tatoeba /etc/nginx/sites-enabled/tatoeba
cp fpm /etc/php5/fpm/pool.d/tatoeba.conf

service nginx restart
update-rc.d -f apache2 remove
update-rc.d nginx defaults
