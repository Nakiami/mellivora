<?php

require(CONFIG_PATH_THIRDPARTY_COMPOSER . 'pear/cache_lite/Cache/Lite/Output.php');

$caches = array();

function cache_start ($identifier, $lifetime, $send_headers = false) {
    global $caches;

    // if lifetime is zero, we don't perform caching.
    // by returning true, we signal that content needs to be recreated
    if (!$lifetime) {
        return true;
    }

    // if no caching object exists for this identifier, create it
    if (empty($caches[$identifier])) {
        $caches[$identifier] = new Cache_Lite_Output(
            array(
                'cacheDir' => CONFIG_PATH_CACHE,
                'lifeTime' => $lifetime,
                'fileNameProtection' => false
            )
        );
    }

    // return true if cache has expired, and we need to recreate content
    // return false if cache is still valid
    return !($caches[$identifier]->start($identifier));
}

function cache_end ($identifier) {
    global $caches;

    if (!empty($caches[$identifier])) {
        $caches[$identifier]->end();
    }
}

function send_cache_headers ($identifier, $lifetime, $group = 'default') {

    header('Cache-Control: '.(user_is_logged_in() ? 'private' : 'public').', max-age=' . $lifetime);

    $path = CONFIG_PATH_CACHE . 'cache_' . $group . '_' . $identifier;
    if (file_exists($path)) {
        $time_modified = filemtime($path);

        header('Last-Modified: ' . gmdate('D, d M Y H:i:s ', $time_modified) . 'GMT');
        header('Expires: ' . gmdate('D, d M Y H:i:s ', $time_modified + $lifetime) . 'GMT');
    }
}