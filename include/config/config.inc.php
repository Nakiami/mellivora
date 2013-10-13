<?php

// * mysql time and php time should be the same
// * see: http://www.php.net/manual/en/timezones.php for zones
// * Instead of using this function to set the default timezone
//   each time a page loads, you should probably use the INI
//   setting "date.timezone" in php.ini. Uncomment if you need
//   to change the time zone and php.ini is unavailable to you.

//const CONFIG_DATE_DEFAULT_TIMEZONE = 'Australia/Sydney';
//date_default_timezone_set(CONFIG_DATE_DEFAULT_TIMEZONE);

// paths below must end in a "/" !
const CONFIG_PATH_MELLIVORA = '/var/www/mellivora/';
// don't change these three unless you know what you're doing
define('CONFIG_PATH_INCLUDE', CONFIG_PATH_MELLIVORA.'include/');
define('CONFIG_PATH_CONFIG', CONFIG_PATH_MELLIVORA.'include/config/');
define('CONFIG_PATH_FILE_UPLOAD', CONFIG_PATH_MELLIVORA.'upload/');
define('CONFIG_PATH_CACHE', CONFIG_PATH_MELLIVORA.'cache/');

// database settings
require(CONFIG_PATH_CONFIG . 'db.inc.php');

// general site settings
const CONFIG_SITE_NAME = 'Mellivora';
const CONFIG_SITE_SLOGAN = 'Mellivora, the CTF engine';
const CONFIG_SITE_DESCRIPTION = '';
const CONFIG_SITE_URL = 'http://sub.domain.com/';
const CONFIG_SITE_LOGO = 'img/favicon.png';

// redirects:
const CONFIG_INDEX_REDIRECT_TO = 'home'; // from index.php
const CONFIG_LOGIN_REDIRECT_TO = 'home'; // after login
const CONFIG_REGISTER_REDIRECT_TO = 'home'; // after successful account registration

// team names longer than 40 chars may break page layout
const CONFIG_MIN_TEAM_NAME_LENGTH = 2;
const CONFIG_MAX_TEAM_NAME_LENGTH = 40;
const CONFIG_ACCOUNTS_SIGNUP_ALLOWED = true;
const CONFIG_ACCOUNTS_DEFAULT_ENABLED = true;
const CONFIG_ACCOUNTS_EMAIL_PASSWORD_ON_SIGNUP = true;

// is site SSL compatible? if true, ssl will be forced on certain pages
const CONFIG_SSL_COMPAT = false;

// maximum file upload size
const CONFIG_MAX_FILE_UPLOAD_SIZE = 5242880;

// a global hardcoded salt applied to all user password hashes
// (in addition to a user-specific salt). if you change this
// after users have been created, they won't be able to log in.
const CONFIG_HASH_SALT = '';

// user classes
const CONFIG_UC_USER = 0;
const CONFIG_UC_MODERATOR = 100;

// email stuff
const CONFIG_EMAIL_USE_SMTP = false;
const CONFIG_EMAIL_FROM_EMAIL = 'you@domain.com';
const CONFIG_EMAIL_FROM_NAME = 'Mellivora CTF';
// blank for same as "FROM"
const CONFIG_EMAIL_REPLYTO_EMAIL = '';
const CONFIG_EMAIL_REPLYTO_NAME = '';
// options:
// 0 = off (for production use)
// 1 = client messages
// 2 = client and server messages
const CONFIG_EMAIL_SMTP_DEBUG_LEVEL = 2;
const CONFIG_EMAIL_SMTP_HOST = 'smtp.gmail.com';
const CONFIG_EMAIL_SMTP_PORT = 587;
const CONFIG_EMAIL_SMTP_SECURITY = 'tls';
// require SMTP authentication?
const CONFIG_EMAIL_SMTP_AUTH = true;
const CONFIG_EMAIL_SMTP_USER = 'you@domain.com';
const CONFIG_EMAIL_SMTP_PASSWORD = '';

// enable re-captcha on signup and various public forms
const CONFIG_RECAPTCHA_ENABLE = false;
const CONFIG_RECAPTCHA_PUBLIC_KEY = '';
const CONFIG_RECAPTCHA_PRIVATE_KEY = '';

// cache times
const CONFIG_CACHE_TIME_SCORES = 0;
const CONFIG_CACHE_TIME_HOME = 0;
const CONFIG_CACHE_TIME_USER = 0;
const CONFIG_CACHE_TIME_CHALLENGE = 0;
const CONFIG_CACHE_TIME_HINTS = 0;