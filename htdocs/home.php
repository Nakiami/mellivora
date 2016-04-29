<?php

require('../include/mellivora.inc.php');

login_session_refresh();

send_cache_headers('home', CONFIG_CACHE_TIME_HOME);

head(lang_get('home'));

if (cache_start(CONST_CACHE_NAME_HOME, CONFIG_CACHE_TIME_HOME)) {

    $news = db_query_fetch_all('SELECT * FROM news ORDER BY added DESC');
    foreach ($news as $item) {
        echo '
        <div class="news-container">';
            section_head($item['title']);
            echo '
            <div class="news-body">
                ',get_bbcode()->parse($item['body']),'
            </div>
        </div>
        ';
    }

    cache_end(CONST_CACHE_NAME_HOME);
}

foot();