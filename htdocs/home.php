<?php

define('IN_FILE', true);
require('../include/general.inc.php');

enforceAuthentication();

head('Home');

$stmt = $db->query('SELECT * FROM news ORDER BY added DESC');
while($news = $stmt->fetch(PDO::FETCH_ASSOC)) {
    sectionHead($news['title']);
    echo formatText($news['body']);
}

foot();