<?php

abstract class Config {

    static private $config = array();

    public static function get($key) {

        if(self::allow_env_override($key) && getenv($key)) {
            return getenv($key);
        }

        return self::$config[$key];
    }

    public static function set($key, $value) {
        return self::$config[$key] = $value;
    }

    private static function allow_env_override($key) {
        if (!defined('RESTRICT_ENV_CONFIG_OVERRIDE')) {
            return true;
        }

        return in_array($key, RESTRICT_ENV_CONFIG_OVERRIDE) || in_array('*', RESTRICT_ENV_CONFIG_OVERRIDE);
    }
}