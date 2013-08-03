<?php

define('IN_FILE', true);
require('../include/general.inc.php');

enforceAuthentication(CONFIG_UC_MODERATOR);

head('Site management');
managementMenu();
sectionHead('List news');

$stmt = $db->query('SELECT * FROM news ORDER BY added DESC');
while($news = $stmt->fetch(PDO::FETCH_ASSOC)) {
    sectionSubHead(htmlspecialchars($news['title']) . ' <a href="edit_news.php?id='.htmlspecialchars($news['id']).'" class="btn btn-mini btn-primary">Edit</a>', false);
    echo $news['body'];
}

foot();