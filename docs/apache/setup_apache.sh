#!/bin/bash

apt-get install -y apache2

cp default /etc/apache2/sites-available
ln -s /etc/apache2/sites-available/default /etc/apache2/sites-enabled/default

service apache2 restart
update-rc.d apache2 defaults
