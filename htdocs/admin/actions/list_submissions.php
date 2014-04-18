<?php

require('../../../include/mellivora.inc.php');

enforce_authentication(CONFIG_UC_MODERATOR);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    validate_id($_POST['id']);

    if ($_POST['action'] == 'delete') {

        db_delete(
            'submissions',
            array(
                'id'=>$_POST['id']
            )
        );

        redirect(CONFIG_SITE_ADMIN_RELPATH . 'list_submissions.php?generic_success=1');
    }

    else if ($_POST['action'] == 'mark_incorrect') {

        db_update('submissions', array('correct'=>0), array('id'=>$_POST['id']));

        redirect(CONFIG_SITE_ADMIN_RELPATH . 'list_submissions.php?generic_success=1');
    }

    else if ($_POST['action'] == 'mark_correct') {

        db_update('submissions', array('correct'=>1), array('id'=>$_POST['id']));

        redirect(CONFIG_SITE_ADMIN_RELPATH . 'list_submissions.php?generic_success=1');
    }
}