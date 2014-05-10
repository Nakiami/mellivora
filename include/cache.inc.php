<?php

require(CONFIG_PATH_THIRDPARTY_COMPOSER . 'pear/cache_lite/Cache/Lite/Output.php');

$caches = array();

function cache_start ($identifier, $lifetime) {
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