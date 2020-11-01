Mellivora on Ubuntu 20.04
=========

This readme serves as a super-quick guide to setting up Mellivora on Ubuntu.

Estimated setup time: 15 minutes.

### Preliminary

Launch and SSH into a clean Ubuntu instance.

### Installation

```sh
sudo apt-get update && sudo apt-get -y upgrade
sudo apt-get -y install tasksel
sudo tasksel install lamp-server
sudo mysql_secure_installation
```

Follow prompts in the MySQL setup, choosing whatever options suit your circumstances.

Install required PHP extensions
```sh
sudo apt-get install php-curl php-pear php-mbstring
```

Install Unzip (Required for Composer)
```sh
sudo apt-get install -y unzip
```

Install Composer
```sh
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
```

Make /var/www/ writable.
```sh
sudo chown -R $(whoami):$(whoami) /var/www/
cd /var/www/
```

Install git and clone the repo.
```sh
sudo apt-get install -y git
git clone https://github.com/Nakiami/mellivora.git
```

Fetch required dependencies using Composer
```sh
cd /var/www/mellivora/
composer install
```

Copy and edit configuration file.
```sh
cp /var/www/mellivora/include/config/config.default.inc.php /var/www/mellivora/include/config/config.inc.php
cp /var/www/mellivora/include/config/db.default.inc.php /var/www/mellivora/include/config/db.inc.php
vim /var/www/mellivora/include/config/config.inc.php
```

Make the writable directory writable.
```sh
sudo chown -R www-data:www-data /var/www/mellivora/writable/
```

Copy and edit the Apache2 config file.
```sh
sudo cp /var/www/mellivora/install/lamp/mellivora.apache.conf /etc/apache2/sites-available/mellivora.conf
sudo vim /etc/apache2/sites-available/mellivora.conf
```

Disable the default Apache site. Activate Mellivora. Restart Apache.
```sh
sudo a2dissite 000-default
sudo a2ensite mellivora
sudo service apache2 restart
```

Create the Mellivora database and import the provided structure.
```sh
echo "CREATE DATABASE mellivora CHARACTER SET utf8 COLLATE utf8_general_ci;" | mysql -u root -p
mysql mellivora -u root -p < /var/www/mellivora/install/sql/001-mellivora.sql
mysql mellivora -u root -p < /var/www/mellivora/install/sql/002-countries.sql
```

Create a new MySQL user. Replace `YourUserName` and `YourPassword` with something sane.
```sh
echo "CREATE USER 'YourUserName'@'%' IDENTIFIED BY 'YourPassword';" | mysql -u root -p
echo "GRANT ALL PRIVILEGES ON mellivora.* TO 'YourUserName'@'%';" | mysql -u root -p
```

Update the database config settings to use the database and user we created above.
```sh
vim /var/www/mellivora/include/config/db.inc.php
```

- Visit https://[yoursite]/ which should now display the Mellivora landing page. If not, the Apache2 config probably needs more attention.
- Register a new user.
- If you get an error about emails not working, you should set up a local mailserver or set up SMTP in the config. The user is created and functional despite the error.

Make the user a site moderator.
```sh
echo "UPDATE users SET class = 100 WHERE id = 1;" | mysql mellivora -u root -p
```

Log in. Done!

### Troubleshooting

- Apache gives a 404 when visiting pages without the .php extension.
 - The easiest way to fix this is to use Apache's "MultiViews". You'll also need to add "AddType application/x-httpd-php .php" to your Apache configuration. See example config in "install/lamp". Alternatively, use mod_rewrite.
