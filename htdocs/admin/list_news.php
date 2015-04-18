<?php

require('../../include/mellivora.inc.php');

enforce_authentication(CONST_USER_CLASS_MODERATOR);

require(CONFIG_PATH_THIRDPARTY . 'nbbc/nbbc.php');

$bbc = new BBCode();
$bbc->SetEnableSmileys(false);

head('Site management');
menu_management();
section_head('List news');

$news = db_query_fetch_all('SELECT * FROM news ORDER BY added DESC');
foreach($news as $item) {
    echo '
        <div class="news-container">';
            section_head($item['title'] . ' <a href="edit_news.php?id='.htmlspecialchars($item['id']).'" class="btn btn-xs btn-primary">Edit</a>', '', false);
    echo '
        <div class="news-body">
                ',$bbc->parse($item['body']),'
            </div>
        </div>
        ';
}

foot();