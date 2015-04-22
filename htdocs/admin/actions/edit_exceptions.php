<?php

require('../../../include/mellivora.inc.php');

enforce_authentication(CONST_USER_CLASS_MODERATOR);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    validate_xsrf_token($_POST['xsrf_token']);

    if ($_POST['action'] == 'delete') {

        if (!$_POST['delete_confirmation']) {
            message_error('Please confirm delete');
        }

        db_delete(
            'exceptions',
            array(
                '1'=>1
            )
        );

        redirect(CONFIG_SITE_ADMIN_RELPATH . 'list_exceptions.php?generic_success=1');
    }
}