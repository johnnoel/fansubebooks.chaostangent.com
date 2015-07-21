#!/usr/bin/env bash

USER=vagrant

locale-gen en_GB en_GB.UTF-8

# latest PHP 5.6 packages
add-apt-repository -y ppa:ondrej/php5-5.6
# latest nodejs packages
add-apt-repository -y ppa:chris-lea/node.js
apt-get update
apt-get install -y acl curl nginx postgresql redis-server nodejs git ruby \
    php5-cli php5-json php5-fpm php5-intl php5-pgsql php5-sqlite php5-curl php5-mcrypt

# install composer
curl -sS https://getcomposer.org/installer | php
mv composer.phar /usr/local/bin/composer
chmod +x /usr/local/bin/composer

# nginx vhost
rm /etc/nginx/sites-available/default
cp /vagrant/provisioning/vagrant/vhost.conf /etc/nginx/sites-available/default

# change PHP ini settings
sed -i 's|;date.timezone =|date.timezone = Europe/London|' /etc/php5/fpm/php.ini
sed -i 's|display_errors = Off|display_errors = On|' /etc/php5/fpm/php.ini
sed -i 's|upload_max_filesize = 2M|upload_max_filesize = 64M|' /etc/php5/fpm/php.ini
sed -i 's|post_max_size = 8M|post_max_size = 64M|' /etc/php5/fpm/php.ini
# change the nginx user (makes things easier )
sed -i "s|user www-data|user $USER|" /etc/nginx/nginx.conf
# update php-fpm to match
sed -i "s|www-data|$USER|" /etc/php5/fpm/pool.d/www.conf

# postgres user
sudo -u postgres createuser fansubebooks
sudo -u postgres createdb -O fansubebooks fansubebooks
sudo -u postgres psql -c "ALTER USER fansubebooks WITH PASSWORD 'fansubebooks';"

service nginx reload
service php5-fpm restart

cd /vagrant

# not installed PHP dependencies? do so now
if [ ! -d "/vagrant/vendor" ]; then
    sudo -u $USER composer install
fi

# not installed nodejs dependencies? do so now
if [ ! -d "/vagrant/node_modules" ]; then
    sudo -u $USER HOME=/home/$USER npm install
fi

# load data fixtures
sudo -u $USER php app/console doctrine:schema:create
