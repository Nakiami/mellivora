Mellivora on EC2 (Ubuntu 14.04)
=========

This readme serves as a super-quick guide to setting up Mellivora on an EC2 instance.
EC2 is Amazon's cloud hosting service.
Instructions will be valid for any Ubuntu 14.04 server setup.

Estimated setup time: 15 minutes.

### Preliminary

Launch and SSH into a clean Ubuntu 14.04 instance. Point a domain name to the instance IP.

### Installation

```sh
sudo apt-get update && sudo apt-get -y upgrade
sudo tasksel
```
Select LAMP server and follow prompts.

Install Composer
```sh
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
```

Make /var/www/ writable.
```sh
sudo chown -R ubuntu:ubuntu /var/www/
cd /var/www/
```

Install git and clone the repo.
```sh
sudo apt-get install -y git
git clone https://github.com/Nakiami/mellivora.git
```

Fetch required dependencies using Composer
```sh
cd /var/www/mellivora/include/thirdparty/composer/
composer install
```

Copy and edit configuration file.
```sh
cp /var/www/mellivora/include/config/config.inc.php.example /var/www/mellivora/include/config/config.inc.php
cp /var/www/mellivora/include/config/db.inc.php.example /var/www/mellivora/include/config/db.inc.php
vim /var/www/mellivora/include/config/config.inc.php
```

Make the writable directory writable.
```sh
sudo chown -R www-data:www-data /var/www/mellivora/writable/
```

Copy and edit the Apache2 config file.
```sh
sudo cp /var/www/mellivora/install/mellivora.apache.conf /etc/apache2/sites-available/mellivora.conf
sudo vim /etc/apache2/sites-available/mellivora.conf
```

Disable the default Apache site. Activate Mellivora and enable SSL support. Restart Apache.
```sh
sudo a2dissite 000-default
sudo a2enmod ssl
sudo a2ensite mellivora
sudo service apache2 restart
```

Create the Mellivora database and import the provided structure.
```sh
echo "CREATE DATABASE mellivora CHARACTER SET utf8 COLLATE utf8_general_ci;" | mysql -u root -p
mysql mellivora -u root -p < /var/www/mellivora/install/mellivora.sql
mysql mellivora -u root -p < /var/www/mellivora/install/countries.sql
```

Create a new MySQL user.
```sh
echo "GRANT ALL PRIVILEGES ON mellivora.* To 'melDbUser'@'%' IDENTIFIED BY 'melDbUserPassword';" | mysql -u root -p
```

Update the database config settings.
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
 - The easiest way to fix this is to use Apache's "MultiViews". You'll also need to add "AddType application/x-httpd-php .php" to your Apache configuration. See example config in "install/". Alternatively, use mod_rewrite.