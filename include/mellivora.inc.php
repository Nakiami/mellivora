<?php

require('config/config.inc.php');
require(CONFIG_PATH_THIRDPARTY_COMPOSER . 'autoload.php');
require(CONFIG_PATH_INCLUDE . 'session.inc.php');
require(CONFIG_PATH_INCLUDE . 'general.inc.php');
require(CONFIG_PATH_INCLUDE . 'graphics.inc.php');
require(CONFIG_PATH_INCLUDE . 'db.inc.php');
require(CONFIG_PATH_INCLUDE . 'cache.inc.php');
require(CONFIG_PATH_INCLUDE . 'json.inc.php');
require(CONFIG_PATH_INCLUDE . 'email.inc.php');

session_set_cookie_params(
    CONFIG_SESSION_TIMEOUT,
    '/',
    null,
    CONFIG_SSL_COMPAT,
    true
);
session_start();