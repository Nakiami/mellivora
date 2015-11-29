<?php

require('../include/mellivora.inc.php');

$user = db_select_one(
    'users',
    array('id', 'enabled'),
    array('download_key'=>$_GET['team_key'])
);

if (!is_valid_id($user['id'])) {
    log_exception(new Exception('Invalid team key used for download'));
    message_error(lang_get('invalid_team_key'));
}

if (!$user['enabled']) {
    message_error(lang_get('user_not_enabled'));
}

$file = db_query_fetch_one('
    SELECT
      f.id,
      f.title,
      f.size,
      f.md5,
      c.available_from
    FROM files AS f
    LEFT JOIN challenges AS c ON c.id = f.challenge
    WHERE f.download_key = :download_key',
    array(
        'download_key'=>$_GET['file_key']
    )
);

if (!is_valid_id($file['id'])) {
    log_exception(new Exception('Invalid file key used for download'));
    message_error(lang_get('no_file_found'));
}

if (time() < $file['available_from'] && !user_is_staff()) {
    message_error(lang_get('file_not_available'));
}

download_file($file);