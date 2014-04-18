<?php

require('../../../include/mellivora.inc.php');

enforce_authentication(CONFIG_UC_MODERATOR);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    validate_id($_POST['id']);

    if ($_POST['action'] == 'edit') {

       db_update(
            'challenges',
            array(
                'title'=>$_POST['title'],
                'description'=>$_POST['description'],
                'flag'=>$_POST['flag'],
                'automark'=>$_POST['automark'],
                'case_insensitive'=>$_POST['case_insensitive'],
                'points'=>$_POST['points'],
                'category'=>$_POST['category'],
                'available_from'=>strtotime($_POST['available_from']),
                'available_until'=>strtotime($_POST['available_until']),
                'num_attempts_allowed'=>$_POST['num_attempts_allowed']
            ),
            array('id'=>$_POST['id'])
        );

        redirect(CONFIG_SITE_ADMIN_RELPATH . 'edit_challenge.php?id='.$_POST['id'].'&generic_success=1');
    }

    else if ($_POST['action'] == 'delete') {

        if (!$_POST['delete_confirmation']) {
            message_error('Please confirm delete');
        }

        delete_challenge_cascading($_POST['id']);

        redirect(CONFIG_SITE_ADMIN_RELPATH . 'manage.php?generic_success=1');
    }

    else if ($_POST['action'] == 'upload_file') {

        if ($_FILES['file']['size'] > max_file_upload_size()) {
            message_error('File too large.');
        }

        $file_id = db_insert(
            'files',
            array(
                'added'=>time(),
                'added_by'=>$_SESSION['id'],
                'title'=>$_FILES['file']['name'],
                'size'=>$_FILES['file']['size'],
                'challenge'=>$_POST['id']
            )
        );

        if (file_exists(CONFIG_PATH_FILE_UPLOAD . $file_id)) {
            message_error('File already existed! This should never happen!');
        }

        else {
            move_uploaded_file($_FILES['file']['tmp_name'], CONFIG_PATH_FILE_UPLOAD . $file_id);
        }

        if (!file_exists(CONFIG_PATH_FILE_UPLOAD . $file_id)) {
            delete_file($file_id);
            message_error('File upload failed!');
        }

        redirect(CONFIG_SITE_ADMIN_RELPATH . 'edit_challenge.php?id='.$_POST['id'].'&generic_success=1');
    }

    else if ($_POST['action'] == 'delete_file') {
        delete_file($_POST['id']);

        redirect(CONFIG_SITE_ADMIN_RELPATH . 'edit_challenge.php?id='.$_POST['challenge_id'].'&generic_success=1');
    }
}