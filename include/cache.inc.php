<?php

require(CONFIG_PATH_THIRDPARTY_COMPOSER . 'pear/cache_lite/Cache/Lite/Output.php');

$caches = array();

function cache_start ($identifier, $lifetime, $group = 'default') {
    global $caches;

    // if lifetime is zero, we don't perform caching.
    // by returning true, we signal that content needs to be recreated
    if (!$lifetime) {
        return true;
    }

    // if no caching object exists for this identifier, create it
    if (empty($caches[$group][$identifier])) {
        $caches[$group][$identifier] = new Cache_Lite_Output(
            array(
                'cacheDir' => CONFIG_PATH_CACHE,
                'lifeTime' => $lifetime,
                'fileNameProtection' => false
            )
        );
    }

    // return true if cache has expired, and we need to recreate content
    // return false if cache is still valid
    return !($caches[$group][$identifier]->start($identifier, $group));
}

function cache_end ($identifier, $group = 'default') {
    global $caches;

    if (!empty($caches[$group][$identifier])) {
        $caches[$group][$identifier]->end();
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

function invalidate_cache ($id, $group = 'default') {
    $path = CONFIG_PATH_CACHE . 'cache_' . $group . '_' . $id;
    if (file_exists($path)) {
        unlink($path);
    }
}

function invalidate_cache_group ($group = 'default') {
    $prefix = 'cache_' . $group . '_';

    $cache_files = scandir(CONFIG_PATH_CACHE);
    foreach ($cache_files as $file) {
        if (starts_with($file, $prefix)) {
            unlink(CONFIG_PATH_CACHE . $file);
        }
    }
}