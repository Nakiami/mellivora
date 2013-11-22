<?php

require('../include/mellivora.inc.php');

head('Home');

$cache = new Cache_Lite_Output(array('cacheDir'=>CONFIG_PATH_CACHE, 'lifeTime'=>CONFIG_CACHE_TIME_HOME));
if (!($cache->start('home'))) {

    require(CONFIG_PATH_THIRDPARTY . 'nbbc/nbbc.php');

    $bbc = new BBCode();
    $bbc->SetEnableSmileys(false);

    $stmt = $db->query('SELECT * FROM news ORDER BY added DESC');
    while($news = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo '
        <div class="news-container">
            ',section_head($news['title']),'
            <div class="news-body">
                ',$bbc->parse($news['body']),'
            </div>
        </div>
        ';
    }

    $cache->end();
}

foot();