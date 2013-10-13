<?php

session_start();

require('config/config.inc.php');
require(CONFIG_PATH_INCLUDE . 'session.inc.php');
require(CONFIG_PATH_INCLUDE . 'general.inc.php');
require(CONFIG_PATH_INCLUDE . 'graphics.inc.php');
require(CONFIG_PATH_THIRDPARTY . 'Cache/Lite/Output.php');

// always connect to database
$db = new PDO(DB_ENGINE.':host='.DB_HOST.';dbname='.DB_NAME.';charset=utf8', DB_USER, DB_PASSWORD);
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);