<?php

if (!defined('IN_FILE')) {
    exit(); // TODO report error
}

require('db.inc.php');

// both paths below must end in a "/" !
define('CONFIG_ABS_PATH', '/var/www/mellivora/');
define('CONFIG_FILE_UPLOAD_PATH', CONFIG_ABS_PATH . 'upload/');

define('CONFIG_SITE_NAME', 'Mellivora');
define('CONFIG_SITE_SLOGAN', 'Mellivora, the CTF engine');
define('CONFIG_SITE_DESCRIPTION', '');
define('CONFIG_SITE_URL', 'http://sub.domain.com/');
define('CONFIG_SITE_LOGO', 'favicon.png');

define('CONFIG_SUMMARY_LENGTH', 255);

define('CONFIG_INDEX_REDIRECT_TO', 'login');
define('CONFIG_LOGIN_REDIRECT_TO', 'home');
define('CONFIG_REGISTER_REDIRECT_TO', 'home');

define('CONFIG_HASH_SALT', '');

define('CONFIG_UC_USER', 0);
define('CONFIG_UC_MODERATOR', 100);

define('CONFIG_SSL_COMPAT', false);

define('CONFIG_MAX_FILE_UPLOAD_SIZE', 5242880);

// team names longer than 40 chars may break page layout
define('CONFIG_MIN_TEAM_NAME_LENGTH', 2);
define('CONFIG_MAX_TEAM_NAME_LENGTH', 40);

define('CONFIG_ACCOUNTS_DEFAULT_ENABLED', true);
define('CONFIG_ACCOUNTS_EMAIL_PASSWORD_ON_SIGNUP', true);

define('CONFIG_EMAIL_FROM_EMAIL', 'you@domain.com');
define('CONFIG_EMAIL_FROM_NAME', 'Mellivora CTF');
// blank for same as "FROM"
define('CONFIG_EMAIL_REPLYTO_EMAIL', '');
define('CONFIG_EMAIL_REPLYTO_NAME', '');
// options: smtp, mail
define('CONFIG_EMAIL_METHOD', 'smtp');
// options:
// 0 = off (for production use)
// 1 = client messages
// 2 = client and server messages
define('CONFIG_EMAIL_SMTP_DEBUG_LEVEL', 2);
define('CONFIG_EMAIL_SMTP_HOST', 'smtp.gmail.com');
define('CONFIG_EMAIL_SMTP_PORT', 587);
define('CONFIG_EMAIL_SMTP_SECURITY', 'tls');

// require SMTP authentication?
define('CONFIG_EMAIL_SMTP_AUTH', true);
define('CONFIG_EMAIL_SMTP_USER', 'you@domain.com');
define('CONFIG_EMAIL_SMTP_PASSWORD', '');