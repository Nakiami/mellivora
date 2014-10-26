<?php

require('../include/mellivora.inc.php');

enforce_authentication();

validate_id($_GET['id']);

$file = db_query_fetch_one('
    SELECT
      f.id,
      f.title,
      f.size,
      c.available_from
    FROM files AS f
    LEFT JOIN challenges AS c ON c.id = f.challenge
    WHERE f.id = :id',
    array('id'=>$_GET['id'])
);

if (empty($file)) {
    message_error('No file found with this ID');
}

if (time() < $file['available_from'] && !user_is_staff()) {
    message_error('This file is not available yet.');
}

download_file($file);