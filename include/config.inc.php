<?php

if (!defined('IN_FILE')) {
    exit(); // TODO report error
}

require('db.inc.php');

define('CONFIG_ABS_PATH', '/var/www/mellivora/');
define('CONFIG_FILE_UPLOAD_PATH', CONFIG_ABS_PATH . 'upload');

define('CONFIG_SITE_NAME', 'Mellivora');
define('CONFIG_SITE_SLOGAN', 'Mellivora, the CTF engine');
define('CONFIG_SITE_DESCRIPTION', '');

define('CONFIG_SUMMARY_LENGTH', 255);

define('CONFIG_INDEX_REDIRECT_TO', 'login');
define('CONFIG_LOGIN_REDIRECT_TO', 'home');
define('CONFIG_REGISTER_REDIRECT_TO', 'home');

define('CONFIG_HASH_SALT', '');

define('CONFIG_UC_USER', 0);
define('CONFIG_UC_MODERATOR', 100);

define('CONFIG_SSL_COMPAT', false);

define('CONFIG_MAX_FILE_UPLOAD_SIZE', 5242880);

define('CONFIG_ACCOUNTS_DEFAULT_ENABLED', true);