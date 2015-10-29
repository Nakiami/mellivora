<?php

$lang = array();

require(CONFIG_PATH_BASE . 'include/language/translations/'.CONST_SITE_DEFAULT_LANGUAGE.'.php');

if (defined('CONFIG_SITE_LANGUAGE') && CONFIG_SITE_LANGUAGE !== CONST_SITE_DEFAULT_LANGUAGE) {
    require(CONFIG_PATH_BASE . 'include/language/translations/' . CONFIG_SITE_LANGUAGE . '.php');
}

function lang_get($message, $replace = array()) {
    global $lang;

    if (!array_get($lang, $message)) {
        log_exception(new Exception('Could not fetch translation for key: ' . $message));
        return $message;
    }

    if (!empty($replace)) {

        $braced_replace = array();
        array_walk($replace, function (&$value, $key) use (&$braced_replace) {
            $braced_replace['{'.$key.'}'] = $value;
        });

        return str_replace(
            array_keys($braced_replace),
            array_values($braced_replace),
            $lang[$message]
        );
    }

    return $lang[$message];
}