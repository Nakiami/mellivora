<?php

if (!defined('IN_FILE')) {
    die; // TODO report error
}
define('CONFIG_ABS_PATH', '/var/www/mellivora/');

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