<?php

require('../include/mellivora.inc.php');

head('Home');

if (cache_start('home', CONFIG_CACHE_TIME_HOME)) {

    require(CONFIG_PATH_THIRDPARTY . 'nbbc/nbbc.php');

    $bbc = new BBCode();
    $bbc->SetEnableSmileys(false);

    $news = db_query('SELECT * FROM news ORDER BY added DESC');
    foreach ($news as $item) {
        echo '
        <div class="news-container">
            ',section_head($item['title']),'
            <div class="news-body">
                ',$bbc->parse($item['body']),'
            </div>
        </div>
        ';
    }

    cache_end('home');
}

foot();