<?php

require(CONFIG_PATH_THIRDPARTY . 'Cache/Lite/Output.php');

$caches = array();

function cache_start ($identifier, $lifetime) {
    global $caches;

    if (empty($caches[$identifier])) {
        $caches[$identifier] = new Cache_Lite_Output(array('cacheDir'=>CONFIG_PATH_CACHE, 'lifeTime'=>$lifetime));
    }

    return !($caches[$identifier]->start($identifier));
}

function cache_end ($identifier) {
    global $caches;

    if (!empty($caches[$identifier])) {
        $caches[$identifier]->end();
    }
}