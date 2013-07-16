<?php

define('IN_FILE', true);
require('../include/general.inc.php');

enforceAuthentication();

head('Home');

//echo '<div class="page-header"><h2>Home</h2></div>';

$stmt = $db->query('SELECT * FROM news ORDER BY added DESC');
while($news = $stmt->fetch(PDO::FETCH_ASSOC)) {
    sectionHead($news['title']);
    echo $news['body'];
}

foot();