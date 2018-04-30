Mellivora on Caddy Server
=========

This readme serves as a super-quick guide to setting up Mellivora on a Caddy Server.

Estimated setup time: 15 minutes.

### Preliminary

Connect to your server

### Installation

Install required PHP extensions
```sh
sudo apt-get install php-curl php-pear php-mbstring
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

Install unzip package
```sh
sudo apt-get install unzip
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

Copy and edit the CaddyFile config file.
```sh
sudo cp /var/www/mellivora/install/caddy/Caddyfile /etc/caddy/Caddyfile
sudo vim /etc/caddy/Caddyfile
```

Install mariadb
```sh
sudo apt-get install mariadb-server
```

Create the Mellivora database and import the provided structure.
```sh
echo "CREATE DATABASE mellivora CHARACTER SET utf8 COLLATE utf8_general_ci;" | mysql -u root -p
mysql mellivora -u root -p < /var/www/mellivora/install/sql/001-mellivora.sql
mysql mellivora -u root -p < /var/www/mellivora/install/sql/002-countries.sql
```

Create a new MySQL user.
```sh
echo "GRANT ALL PRIVILEGES ON mellivora.* TO 'YourUserName'@'%' IDENTIFIED BY 'YourPassword';" | mysql -u root -p
```

Update the database config settings to use the database and user we created above.
```sh
vim /var/www/mellivora/include/config/db.inc.php
```

- Visit https://ctf.[yoursite]/ which should now display the Mellivora landing page.
- Register a new user.
- If you get an error about emails not working, you should set up a local mailserver or set up SMTP in the config. The user is created and functional despite the error.

Make the user a site moderator.
```sh
echo "UPDATE users SET class = 100 WHERE id = 1;" | mysql mellivora -u root -p
```

Log in. Done!
