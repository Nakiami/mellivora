<?php

require('../include/mellivora.inc.php');

enforce_authentication();

validate_id($_GET['id']);

$stmt = $db->prepare('
                SELECT
                  f.id,
                  f.title,
                  c.available_from
                FROM
                  files AS f
                LEFT JOIN challenges AS c ON c.id = f.challenge
                WHERE f.id = :id
                ');
$stmt->execute(array(':id' => $_GET['id']));
$file = $stmt->fetch(PDO::FETCH_ASSOC);

if (time() < $file['available_from'] && !user_is_staff()) {
    message_error('This file is not available yet.');
}

$filePath = CONFIG_PATH_FILE_UPLOAD . $file['id'];

if (!is_readable($filePath)) {
    log_exception(new Exception("Could not read the requested file: " . $filePath));
    message_error("Could not read the requested file. An error report has been lodged.");
}

// required for IE, otherwise Content-disposition is ignored
if(ini_get('zlib.output_compression')) {
    ini_set('zlib.output_compression', 'Off');
}

header('Pragma: public');
header('Expires: 0');

header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
header('Cache-Control: private', false); // required for certain browsers

header('Content-Type: application/force-download');
header('Content-Disposition: attachment; filename="'.basename($file['title']).'";');
header('Content-Transfer-Encoding: binary');
header('Content-Length: '.filesize($filePath));

readfile($filePath);