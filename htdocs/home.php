<?php

define('IN_FILE', true);
require('../include/general.inc.php');
require(CONFIG_ABS_PATH . 'include/nbbc/nbbc.php');

//enforceAuthentication();

head('Home');

$bbc = new BBCode();
$bbc->SetEnableSmileys(false);

$stmt = $db->query('SELECT * FROM news ORDER BY added DESC');
while($news = $stmt->fetch(PDO::FETCH_ASSOC)) {
    section_head($news['title']);
    echo $bbc->parse($news['body']);
}

foot();