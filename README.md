mellivora
=========

Mellivora is a basic database driven CTF engine written in PHP.

### Requirements

- LAMP: PHP 5.3+, MySQL 5.5+, Apache 2.2+. May work with other configurations but this is untested.

### Installation

- Download to any directory, say: "/var/www/mellivora/".
- Create an Apache VHost and point DocumentRoot to "htdocs/". An example Apache config can be found in the "install/" directory.
- Create a database and import the structure from "install/db.sql".
- Edit "config.inc.php" and "db.inc.php" in "config/".
- Make the "writable/" directory and subdirectories writable by Apache.

### Troubleshooting

- Apache gives a 404 when visiting pages without the .php extension.
 - The easiest way to fix this is to use Apache's "MultiViews". You'll also need to add "AddType application/x-httpd-php .php" to your Apache configuration. See example config in "install/". Alternatively, use mod_rewrite.

### License

This software is licenced under the [GNU General Public License v3 (GPL-3)](http://www.tldrlegal.com/license/gnu-general-public-license-v3-%28gpl-3%29). The "include/thirdparty/" directory contains third party code. Please read their LICENSE files for information on the software availability and distribution.