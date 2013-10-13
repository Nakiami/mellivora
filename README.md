mellivora
=========

Mellivora is a basic database driven CTF engine written in PHP.

### Requirements

- LAMP: PHP 5.3+, MySQL 5.5+.
- May work on other OSs/DBMSs but this is untested.

### Installation

- Download to any directory.
- Create an Apache VHost and point DocumentRoot to "htdocs".
- Create a database and import db.sql.
- Edit config.inc.php and db.inc.php in config/.
- Make the writable/ directory writable by Apache.

### License

This software is licenced under the [GNU General Public License v3 (GPL-3)](http://www.tldrlegal.com/license/gnu-general-public-license-v3-%28gpl-3%29). The include/ directory contains third party code. Please read their LICENSE files for information on the software availability and distribution.