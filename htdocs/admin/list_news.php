<?php

require('../../include/mellivora.inc.php');

enforce_authentication(CONST_USER_CLASS_MODERATOR);

head('Site management');
menu_management();
section_head('List news', button_link('Add news item','new_news'), false);

$news = db_query_fetch_all('SELECT * FROM news ORDER BY added DESC');
foreach($news as $item) {
    echo '
        <div class="news-container">';
            section_head($item['title'] . ' <a href="edit_news.php?id='.htmlspecialchars($item['id']).'" class="btn btn-xs btn-primary">Edit</a>', '', false);
    echo '
        <div class="news-body">
                ',get_bbcode()->parse($item['body']),'
            </div>
        </div>
        ';
}

foot();