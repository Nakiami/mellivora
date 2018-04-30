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
        return in_array($key, ALLOW_ENV_CONFIG_OVERRIDE) || in_array('*', ALLOW_ENV_CONFIG_OVERRIDE);
    }
}