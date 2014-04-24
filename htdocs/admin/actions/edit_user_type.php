<?php

require('../../../include/mellivora.inc.php');

enforce_authentication(CONFIG_UC_MODERATOR);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    validate_id($_POST['id']);

    if ($_POST['action'] == 'edit') {

        db_update(
          'user_types',
          array(
             'title'=>$_POST['title'],
             'description'=>$_POST['description']
          ),
          array(
             'id'=>$_POST['id']
          )
        );

        redirect(CONFIG_SITE_ADMIN_RELPATH . 'list_user_types.php?generic_success=1');
    }

    else if ($_POST['action'] == 'delete') {

        if (!$_POST['delete_confirmation']) {
            message_error('Please confirm delete');
        }

        db_delete(
            'user_types',
            array(
                'id'=>$_POST['id']
            )
        );

        redirect(CONFIG_SITE_ADMIN_RELPATH . 'list_user_types.php?generic_success=1');
    }
}