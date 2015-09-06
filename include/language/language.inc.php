<?php

$lang = array();

require(CONFIG_PATH_BASE . 'include/language/translations/'.CONST_SITE_DEFAULT_LANGUAGE.'.php');

if (!empty(CONFIG_SITE_LANGUAGE) && CONFIG_SITE_LANGUAGE !== CONST_SITE_DEFAULT_LANGUAGE) {
    require(CONFIG_PATH_BASE . 'include/language/translations/' . CONFIG_SITE_LANGUAGE . '.php');
}

function lang_get($message, $replace = array()) {
    global $lang;

    if (!empty($replace)) {
        return str_replace(
            array_walk(array_keys($replace), 'add_braces'),
            array_values($replace),
            $lang[$message]
        );
    }

    return $lang[$message];
}

function add_braces(&$key) {
    $key = '{'.$key.'}';
}