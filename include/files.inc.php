<?php

use Aws\S3\S3Client;

function store_file($challenge_id, $file) {
    if ($file['error']) {
        message_error('Could not upload file: ' . file_upload_error_description($file['error']));
    }

    if ($file['size'] > max_file_upload_size()) {
        message_error('File too large.');
    }

    $file_id = db_insert(
        'files',
        array(
            'added'=>time(),
            'added_by'=>$_SESSION['id'],
            'title'=>$file['name'],
            'size'=>$file['size'],
            'challenge'=>$challenge_id
        )
    );

    if (file_exists(CONFIG_PATH_FILE_UPLOAD . $file_id)) {
        message_error('File already existed! This should never happen!');
    }

    // do we put the file on AWS S3?
    if (CONFIG_AWS_S3_KEY_ID && CONFIG_AWS_S3_SECRET && CONFIG_AWS_S3_BUCKET) {
        try {
            // Instantiate the S3 client with your AWS credentials
            $client = S3Client::factory(array(
                'key'    => CONFIG_AWS_S3_KEY_ID,
                'secret' => CONFIG_AWS_S3_SECRET,
            ));

            $file_key = '/challenges/' . $file_id;

            // Upload an object by streaming the contents of a file
            $result = $client->putObject(array(
                'Bucket'     => CONFIG_AWS_S3_BUCKET,
                'Key'        => $file_key,
                'SourceFile' => $file['tmp_name']
            ));

            // We can poll the object until it is accessible
            $client->waitUntil('ObjectExists', array(
                'Bucket' => CONFIG_AWS_S3_BUCKET,
                'Key'    => $file_key
            ));
        } catch (Exception $e) {
            message_error('Caught exception uploading file to S3: ' . $e->getMessage());
        }
    }

    // or store the file locally?
    else {
        move_uploaded_file($file['tmp_name'], CONFIG_PATH_FILE_UPLOAD . $file_id);
        if (!file_exists(CONFIG_PATH_FILE_UPLOAD . $file_id)) {
            delete_file($file_id);
            message_error('File upload failed!');
        }
    }
}

function download_file($file) {
    validate_id(array_get($file, 'id'));

    // do we read the file off AWS S3?
    if (CONFIG_AWS_S3_KEY_ID && CONFIG_AWS_S3_SECRET && CONFIG_AWS_S3_BUCKET) {
        try {
            // Instantiate the S3 client with your AWS credentials
            $client = S3Client::factory(array(
                'key'    => CONFIG_AWS_S3_KEY_ID,
                'secret' => CONFIG_AWS_S3_SECRET,
            ));

            $file_key = '/challenges/' . $file['id'];

            $client->registerStreamWrapper();

            // Send a HEAD request to the object to get headers
            $command = $client->getCommand('HeadObject', array(
                'Bucket' => CONFIG_AWS_S3_BUCKET,
                'Key'    => $file_key
            ));
             
            $filePath = 's3://'.CONFIG_AWS_S3_BUCKET . $file_key;

        } catch (Exception $e) {
            message_error('Caught exception uploading file to S3: ' . $e->getMessage());
        }
    }
    // or read it locally?
    else {
        $filePath = CONFIG_PATH_FILE_UPLOAD . $file['id'];

        if (!is_readable($filePath)) {
            log_exception(new Exception("Could not read the requested file: " . $filePath));
            message_error("Could not read the requested file. An error report has been lodged.");
        }
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
    header('Content-Disposition: attachment; filename="'.$file['title'].'";');
    header('Content-Transfer-Encoding: binary');
    header('Content-Length: '.$file['size']);

    // Stop output buffering
    if (ob_get_level()) {
        ob_end_flush();
    }
    
    flush();

    readfile($filePath);
}

function get_file_name($path) {
    return pathinfo(basename($path), PATHINFO_FILENAME);
}

function file_upload_error_description($code) {
    switch ($code) {
        case UPLOAD_ERR_INI_SIZE:
            return 'The uploaded file exceeds the upload_max_filesize directive in php.ini';
        case UPLOAD_ERR_FORM_SIZE:
            return 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form';
        case UPLOAD_ERR_PARTIAL:
            return 'The uploaded file was only partially uploaded';
        case UPLOAD_ERR_NO_FILE:
            return 'No file was uploaded';
        case UPLOAD_ERR_NO_TMP_DIR:
            return 'Missing a temporary folder';
        case UPLOAD_ERR_CANT_WRITE:
            return 'Failed to write file to disk';
        case UPLOAD_ERR_EXTENSION:
            return 'File upload stopped by extension';
        default:
            return 'Unknown upload error';
    }
}