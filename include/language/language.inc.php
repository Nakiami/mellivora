<?php

$lang = array();

require(Config::get('MELLIVORA_CONFIG_PATH_BASE') . 'include/language/translations/'.CONST_SITE_DEFAULT_LANGUAGE.'.php');

$language_config = Config::get('MELLIVORA_CONFIG_SITE_LANGUAGE');
if ($language_config && $language_config !== CONST_SITE_DEFAULT_LANGUAGE) {
    require(Config::get('MELLIVORA_CONFIG_PATH_BASE') . 'include/language/translations/' . $language_config . '.php');
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