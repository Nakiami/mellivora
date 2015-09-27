<?php

require('../include/mellivora.inc.php');

enforce_authentication(
    CONST_USER_CLASS_USER,
    true
);

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
    message_error(lang_get('no_file_found'));
}

if (time() < $file['available_from'] && !user_is_staff()) {
    message_error(lang_get('file_not_available'));
}

download_file($file);