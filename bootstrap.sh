#!/usr/bin/env bash
if ! [ -L /etc/apache2/site-enabled/ml-frontend.conf ]; then
  sudo ln -s /etc/menulog/ml-frontend.conf /etc/apache2/sites-enabled/ml-frontend.conf
  # restart apache
  sudo apachectl restart
fi

# lnk local config for frontend
if ! [ -L /var/www/menulog/frontend-desktop/includes/configs/config.local.php ]; then
  sudo ln -s /etc/menulog/frontend-desktop/config.vagrant.php /var/www/menulog/frontend-desktop/includes/configs/config.local.php
fi

if ! [ -L /var/www/menulog/frontend-mobile/main_config.local.php ]; then
  sudo ln -s /etc/menulog/frontend-mobile/main_config.vagrant.php /var/www/menulog/frontend-mobile/main_config.local.php
fi

if ! [ -L /var/www/menulog/frontend-admin/main_config.vagrant.php ]; then
  sudo cp /etc/menulog/frontend-admin/main_config.vagrant.php /var/www/menulog/frontend-admin/
fi

# backwards compatiblity
if ! [ -L /var/www/menulog/menulog2 ]; then
  sudo ln -s /var/www/menulog/frontend-desktop /var/www/menulog/menulog2
fi

# remove conf for js mount
if [ -L /etc/apache2/conf-enabled/javascript-common.conf ]; then
  sudo rm /etc/apache2/conf-enabled/javascript-common.conf
fi
