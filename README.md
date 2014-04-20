Mellivora
=========

Mellivora is a basic database driven CTF engine written in PHP.

### Requirements

- LAMP: PHP 5.3.7+, MySQL 5.5+, Apache 2.2+. Will likely work with other configurations but this is untested.

### Installation

- Download to any directory, say: "/var/www/mellivora/".
- Create an Apache VHost and point DocumentRoot to "htdocs/". An example Apache config can be found in the "install/" directory.
- Create a database and import the structure from "install/db.sql".
- Edit "config.inc.php" and "db.inc.php" in the "config/" directory.
- Make the "writable/" directory and its subdirectories writable by Apache.
- Create a user by signing up to the site. Make the user an administrator by manually changing its "class" value to "CONFIG_UC_MODERATOR" (default: 100) in the "users" table in the database.

### Troubleshooting

- Apache gives a 404 when visiting pages without the .php extension.
 - The easiest way to fix this is to use Apache's "MultiViews". You'll also need to add "AddType application/x-httpd-php .php" to your Apache configuration. See example config in "install/". Alternatively, use mod_rewrite.

### Performance
Mellivora is lightweight. And fast. Very fast. Want to run a large competition on an EC2 micro instance? No problem probably! Benchmarks are hard, and often unhelpful. Here are some anyway, running locally on Mellivora's heaviest page (scores) using SIEGE 3.0.5. Some other popular PHP apps added for comparison.

**System**
```
$ uname -a
Linux hostname 3.13.0-24-generic #46-Ubuntu (Ubuntu 14.04 LTS)

$ php -v
PHP 5.5.9-1ubuntu4 (cli) (built: Apr  9 2014 17:08:27) 
Copyright (c) 1997-2014 The PHP Group
Zend Engine v2.5.0, Copyright (c) 1998-2014 Zend Technologies
    with Zend OPcache v7.0.3, Copyright (c) 1999-2014, by Zend Technologies
    with Xdebug v2.2.3, Copyright (c) 2002-2013, by Derick Rethans
    
$ mysql --version
mysql Ver 14.14 Distrib 5.5.35, for debian-linux-gnu (i686) using readline 6.3
```

**With built-in caching disabled (you'd only want this setting for dev)**
```
$ siege -b -t60S https://mellivora/scores

Transactions: 5958 hits
Availability: 100.00 %
Elapsed time:	59.99 secs
Data transferred: 22.02 MB
Response time: 0.15 secs
Transaction rate: 99.32 trans/sec
Throughput: 0.37 MB/sec
Concurrency: 14.93
Successful transactions: 5958
Failed transactions: 0
Longest transaction: 0.30
Shortest transaction: 0.04
```

**With built-in caching enabled (what you'd be running in prod)**
```
$ siege -b -t60S https://mellivora/scores

Transactions: 17041 hits
Availability: 100.00 %
Elapsed time: 59.17 secs
Data transferred: 62.97 MB
Response time: 0.05 secs
Transaction rate: 288.00 trans/sec
Throughput: 1.06 MB/sec
Concurrency: 14.72
Successful transactions: 17041
Failed transactions: 0
Longest transaction: 0.17
Shortest transaction: 0.01
```

**WordPress 3.9, straight out of the box**
```
$ siege -b -t60S https://wordpress

Transactions: 345 hits
Availability: 100.00 %
Elapsed time: 59.29 secs
Data transferred: 0.77 MB
Response time: 2.52 secs
Transaction rate: 5.82 trans/sec
Throughput: 0.01 MB/sec
Concurrency: 14.68
Successful transactions: 346
Failed transactions: 0
Longest transaction: 3.43
Shortest transaction:1.04
```
**A static file, with content: "test"**
```
$ siege -b -t60S https://localhost/test.html

Transactions: 21760 hits
Availability: 100.00 %
Elapsed time: 59.12 secs
Data transferred: 0.10 MB
Response time: 0.04 secs
Transaction rate: 368.06 trans/sec
Throughput: 0.00 MB/sec
Concurrency: 14.51
Successful transactions: 21761
Failed transactions: 0
Longest transaction: 0.18
Shortest transaction: 0.00
```
![Mellivora benchmark chart](http://i.imgur.com/5MgwBrM.png "Mellivora benchmark chart")

as you can see, Mellivora is pretty damn fast.

### License

This software is licenced under the [GNU General Public License v3 (GPL-3)](http://www.tldrlegal.com/license/gnu-general-public-license-v3-%28gpl-3%29). The "include/thirdparty/" directory contains third party code. Please read their LICENSE files for information on the software availability and distribution.
