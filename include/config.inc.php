<?php

if (!defined('IN_FILE')) {
    exit(); // TODO report error
}

// mysql time and php time should be the same
// see: http://www.php.net/manual/en/timezones.php for zones
define('CONFIG_DATE_DEFAULT_TIMEZONE', 'Australia/Sydney');
date_default_timezone_set(CONFIG_DATE_DEFAULT_TIMEZONE);

// paths below must end in a "/" !
define('CONFIG_ABS_PATH', '/var/www/mellivora/');
// don't change these three unless you know what you're doing
define('CONFIG_ABS_INCLUDE_PATH', CONFIG_ABS_PATH . 'include/');
define('CONFIG_FILE_UPLOAD_PATH', CONFIG_ABS_PATH . 'upload/');
define('CONFIG_CACHE_PATH', CONFIG_ABS_PATH . 'cache/');

// don't forget to edit the database settings
require(CONFIG_ABS_INCLUDE_PATH . 'db.inc.php');

// general site settings
define('CONFIG_SITE_NAME', 'Mellivora');
define('CONFIG_SITE_SLOGAN', 'Mellivora, the CTF engine');
define('CONFIG_SITE_DESCRIPTION', '');
define('CONFIG_SITE_URL', 'http://sub.domain.com/');
define('CONFIG_SITE_LOGO', 'favicon.png');

// redirects:
// from index.php
// after login
// after successful account registration
define('CONFIG_INDEX_REDIRECT_TO', 'home');
define('CONFIG_LOGIN_REDIRECT_TO', 'home');
define('CONFIG_REGISTER_REDIRECT_TO', 'home');

// a global hardcoded salt applied to all user password hashes
// if you change this after users have been created, their
// passwords won't work.
define('CONFIG_HASH_SALT', '');

// user classes
define('CONFIG_UC_USER', 0);
define('CONFIG_UC_MODERATOR', 100);

// is site SSL compatible?
define('CONFIG_SSL_COMPAT', false);

define('CONFIG_MAX_FILE_UPLOAD_SIZE', 5242880);

// team names longer than 40 chars may break page layout
define('CONFIG_MIN_TEAM_NAME_LENGTH', 2);
define('CONFIG_MAX_TEAM_NAME_LENGTH', 40);

define('CONFIG_ACCOUNTS_SIGNUP_ALLOWED', true);
define('CONFIG_ACCOUNTS_DEFAULT_ENABLED', true);
define('CONFIG_ACCOUNTS_EMAIL_PASSWORD_ON_SIGNUP', true);

// email stuff
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

// enable re-captcha on signup form
define('CONFIG_RECAPTCHA_ENABLE', false);
define('CONFIG_RECAPTCHA_PUBLIC_KEY', '');
define('CONFIG_RECAPTCHA_PRIVATE_KEY', '');

// cache times
define('CONFIG_CACHE_TIME_SCORES', 0);
define('CONFIG_CACHE_TIME_HOME', 0);
define('CONFIG_CACHE_TIME_USER', 0);
define('CONFIG_CACHE_TIME_CHALLENGE', 0);