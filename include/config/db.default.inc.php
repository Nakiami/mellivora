<?php

/**
 *
 * This file contains default configuration.
 *
 *        DO NOT MAKE CHANGES HERE
 *
 * Copy this file and name it "db.inc.php"
 * before making any changes. Any changes in
 * db.inc.php will override the default
 * config. It is also possible to override
 * configuration options using environment
 * variables. Environment variables override
 * both the default settings and the hard-coded
 * user defined settings.
 *
 */

Config::set('MELLIVORA_CONFIG_DB_ENGINE', 'mysql');
Config::set('MELLIVORA_CONFIG_DB_HOST', 'localhost');
Config::set('MELLIVORA_CONFIG_DB_PORT', 3306);
Config::set('MELLIVORA_CONFIG_DB_NAME', 'mellivora');
Config::set('MELLIVORA_CONFIG_DB_USER', 'root');
Config::set('MELLIVORA_CONFIG_DB_PASSWORD', '');