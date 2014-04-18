<?php

require('../include/mellivora.inc.php');

enforce_authentication(CONFIG_UC_MODERATOR);

require(CONFIG_PATH_THIRDPARTY . 'nbbc/nbbc.php');

$bbc = new BBCode();
$bbc->SetEnableSmileys(false);

head('Site management');
menu_management();
section_head('List news');

$stmt = $db->query('SELECT * FROM news ORDER BY added DESC');
while($news = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo '
        <div class="news-container">
            ',section_head($news['title'] . ' <a href="edit_news.php?id='.htmlspecialchars($news['id']).'" class="btn btn-xs btn-primary">Edit</a>', '', false),'
            <div class="news-body">
                ',$bbc->parse($news['body']),'
            </div>
        </div>
        ';
}

foot();