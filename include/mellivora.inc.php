<?php

require('config/config.inc.php');
require(Config::get('MELLIVORA_CONFIG_PATH_BASE') . 'include/constants.inc.php');
require(Config::get('MELLIVORA_CONFIG_PATH_BASE') . 'include/language/language.inc.php');
require(CONST_PATH_THIRDPARTY_COMPOSER . 'autoload.php');
require(CONST_PATH_INCLUDE . 'session.inc.php');
require(CONST_PATH_INCLUDE . 'raceconditions.inc.php');
require(CONST_PATH_INCLUDE . 'xsrf.inc.php');
require(CONST_PATH_INCLUDE . 'general.inc.php');
require(CONST_PATH_INCLUDE . 'db.inc.php');
require(CONST_PATH_INCLUDE . 'cache.inc.php');
require(CONST_PATH_INCLUDE . 'json.inc.php');
require(CONST_PATH_INCLUDE . 'email.inc.php');
require(CONST_PATH_INCLUDE . 'files.inc.php');
require(CONST_PATH_INCLUDE . 'captcha.inc.php');
require(CONST_PATH_INCLUDE . 'two_factor_auth.inc.php');
require(CONST_PATH_LAYOUT . 'layout.inc.php');
require(CONST_PATH_THIRDPARTY . 'nbbc/nbbc.php');

set_exception_handler('log_exception');

session_set_cookie_params(
    Config::get('MELLIVORA_CONFIG_SESSION_TIMEOUT'),
    '/',
    null,
    Config::get('MELLIVORA_CONFIG_SSL_COMPAT'),
    true
);
session_start();