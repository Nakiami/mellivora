Mellivora
=========

Mellivora is a CTF engine written in PHP. Want a quick overview? Check out a [screenshot gallery on imgur](http://mellivora.imgur.com/).

<p align="center">
  <img src="https://cdn.rawgit.com/Nakiami/mellivora/master/htdocs/img/mellivora.svg" width="250" alt="Mellivora logo"/>
</p>

### Requirements
- LAMP: PHP 5.3.7+, MySQL 5.5+, Apache 2.2+. Will likely work with other configurations but this is untested.
- [Composer](https://getcomposer.org/) dependency manager for PHP.

### Scalable
Mellivora scales well on Amazon Elastic Beanstalk and has support for S3 file storage.

### Performant
Mellivora is lightweight. And fast. Very fast. Want to run a large competition on an EC2 micro instance? No problem!? See [benchmarks.md](benchmarks.md) for some possibly unhelpful benchmarks.

### Installation
You can find detailed setup instructions in [install/README.md](install/README.md). In short:

- Download to any directory, say: "/var/www/mellivora/".
- Run "composer install" in the "include/thirdparty/composer/" directory to install all required dependencies.
- Create an Apache VHost and point DocumentRoot to "htdocs/". An example Apache config can be found in the "install/" directory.
- Create a database and import the structure from "install/db.sql". Import country list from "install/countries.sql".
- In the "config/" directory: copy .example files to "config.inc.php" and "db.inc.php". Edit both to suit your setup.
- Make the "writable/" directory and its subdirectories writable by Apache.
- Create a user by signing up to the site. Make the user an administrator by manually changing its "class" value to "CONST_USER_CLASS_MODERATOR" (default: 100) in the "users" table in the database.

### License
This software is licenced under the [GNU General Public License v3 (GPL-3)](http://www.tldrlegal.com/license/gnu-general-public-license-v3-%28gpl-3%29). The "include/thirdparty/" directory contains third party code. Please read their LICENSE files for information on the software availability and distribution.