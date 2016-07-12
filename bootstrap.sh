#!/usr/bin/env bash
if ! [ -L /etc/menulog/ml-frontend.conf ]; then
  sudo ln -s /etc/menulog/ml-frontend.conf /etc/apache2/conf-enabled/ml-frontend.conf
  # restart apache
  sudo apachectl restart
fi

# lnk local config for frontend
if ! [ -L /var/www/menulog/frontend-desktop/includes/configs/config.local.php ]; then
  sudo ln -s /etc/menulog/frontend-desktop/config.vagrant.php /var/www/menulog/frontend-desktop/includes/configs/config.local.php
fi