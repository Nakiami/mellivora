<?php

define('IN_FILE', true);
require('../include/general.inc.php');

enforceAuthentication();

$stmt = $db->prepare('SELECT id, title FROM files WHERE id = :id');
$stmt->execute(array(':id' => $_GET['id']));
$file = $stmt->fetch(PDO::FETCH_ASSOC);

$realFile = CONFIG_FILE_UPLOAD_PATH . $file['id'];

// TODO check permissions to file?

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
header('Content-Length: '.filesize($realFile));

readfile($realFile);