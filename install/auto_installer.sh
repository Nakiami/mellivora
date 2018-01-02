#!/bin/bash

echo "======================================================"
echo "Welcome to Mellivora CTF auto-installer"
echo "Auto Installer works on any Ubuntu 16.04 server setup."
echo "======================================================"
read -n 1 -s -r -p "Press any key to start the installation."
echo ""

# Updating & Upgrading

sudo apt-get update && sudo apt-get -y upgrade

echo "======================================================"
echo "Select **LAMP server** with space and then follow prompts."
echo "======================================================"
read -n 1 -s -r -p "Press any key to start"
echo ""

sudo apt-get -y install lamp-server^

# Installing nano instead of vim

sudo apt-get -y install nano

# Installing required PHP extensions

sudo apt-get -y install php-curl php-pear php-mbstring

# Making sure curl is installed

sudo apt-get -y install curl

# Install Composer

curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# Make /var/www/ writable.

sudo chown -R $(whoami):$(whoami) /var/www/
cd /var/www/

# Install git and clone the repo.

sudo apt-get install -y git
git clone https://github.com/Nakiami/mellivora.git

# Fetch required dependencies using Composer

cd /var/www/mellivora/
composer install

# Copy and edit configuration file.

cp /var/www/mellivora/include/config/config.inc.php.example /var/www/mellivora/include/config/config.inc.php
cp /var/www/mellivora/include/config/db.inc.php.example /var/www/mellivora/include/config/db.inc.php

echo "======================================================"
read -n 1 -p "Would you like to edit mellivora config.inc.php file? (y/n) " ans;
echo ""
case $ans in
    y|Y)
        sudo nano /var/www/mellivora/include/config/config.inc.php;;
    n|N)
	echo ""
	echo ""
	echo "Ok, if you wish to modify it later the location is:"
	echo "/var/www/mellivora/include/config/config.inc.php"
	read -n 1 -s -r -p "Press any key to continue"
        ;;
    *)
        exit;;
esac

# Make the writable directory writable.

sudo chown -R www-data:www-data /var/www/mellivora/writable/

# Copy and edit the Apache2 config file.

sudo cp /var/www/mellivora/install/mellivora.apache.conf /etc/apache2/sites-available/mellivora.conf

read -n 1 -p "Would you like to edit Apache2 config file? (y/n) " ans;

case $ans in
    y|Y)
        sudo nano /etc/apache2/sites-available/mellivora.conf;;
    n|N)
	echo ""
	echo ""
	echo "Ok, if you wish to modify it later the location is:"
	echo "/etc/apache2/sites-available/mellivora.conf"
	read -n 1 -s -r -p "Press any key to continue"
        ;;
    *)
        exit;;
esac


# Disable the default Apache site. Activate Mellivora. Restart Apache.

sudo a2dissite 000-default
sudo a2ensite mellivora
sudo service apache2 restart


# Create the Mellivora database and import the provided structure.

read -s -p "MySQL root password: " MYSQL_ROOT_PASSWORD

echo "CREATE DATABASE mellivora CHARACTER SET utf8 COLLATE utf8_general_ci;" | mysql -u root -p"${MYSQL_ROOT_PASSWORD}"
mysql mellivora -u root -p"${MYSQL_ROOT_PASSWORD}" < /var/www/mellivora/install/mellivora.sql
mysql mellivora -u root -p"${MYSQL_ROOT_PASSWORD}" < /var/www/mellivora/install/countries.sql


# Create a new MySQL user.
read -n 1 -s -r -p "We will now create a new MySQL user for Mellivora to use.. Press any key to continue"
read -p "New MySQL username (not root): " NEW_MYSQL_USER
read -s -p "Password for the new user: " NEW_MYSQL_USER_PASSWORD
echo "GRANT ALL PRIVILEGES ON mellivora.* TO '${NEW_MYSQL_USER}'@'%' IDENTIFIED BY '${NEW_MYSQL_USER_PASSWORD}';" | mysql -u root -p"${MYSQL_ROOT_PASSWORD}"
echo ""
echo "=========================================="
echo "==========Mellivora Config File==========="
echo "=========================================="
echo "nano will now open so that you can edit Mellivora's config file."
echo "Fill the DB_PASSWORD field with the password you used when installing MySQL previously."
echo "If you want to modify it again after the installation, the location is:"
echo "/var/www/mellivora/include/config/db.inc.php"
read -n 1 -s -r -p "Press any key to continue"
echo ""
echo ""
nano /var/www/mellivora/include/config/db.inc.php

echo ""
echo "=========================================="
echo "========Create moderator account=========="
echo "=========================================="
echo "1) Visit https://[yoursite]/ or http://localhost/ that should now display the Mellivora landing page."
echo "(If Mellivora is not displayed, the Apache2 config probably needs more attention.)"
echo ""
echo "2) Register a new user that will be the moderator account."
echo "(If you get an error about emails not working, you should set up a local mailserver or set up SMTP in the config.)"
echo ""
echo "If you have registered the first account then"
read -n 1 -s -r -p "Press any key to continue and give moderator permissions to it."

echo "UPDATE users SET class = 100 WHERE id = 1;" | mysql mellivora -u root -p

echo ""
echo "Success!! Installation completed"
echo ""
