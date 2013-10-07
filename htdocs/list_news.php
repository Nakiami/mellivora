<?php

require('../include/general.inc.php');

enforce_authentication(CONFIG_UC_MODERATOR);

head('Site management');
menu_management();
section_head('List news');

$stmt = $db->query('SELECT * FROM news ORDER BY added DESC');
while($news = $stmt->fetch(PDO::FETCH_ASSOC)) {
    section_subhead(htmlspecialchars($news['title']) . ' <a href="edit_news.php?id='.htmlspecialchars($news['id']).'" class="btn btn-mini btn-primary">Edit</a>', false);
    echo $news['body'];
}

foot();