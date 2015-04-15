<?php

require('../../../include/mellivora.inc.php');

enforce_authentication(CONFIG_UC_MODERATOR);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

   validate_id($_POST['id']);
    validate_xsrf_token($_POST[CONST_XSRF_TOKEN_KEY]);

    if ($_POST['action'] == 'edit') {

        db_update(
           'hints',
           array(
              'body'=>$_POST['body'],
              'challenge'=>$_POST['challenge'],
              'visible'=>$_POST['visible']
           ),
           array(
              'id'=>$_POST['id']
           )
        );

        invalidate_cache('hints');

        redirect(CONFIG_SITE_ADMIN_RELPATH . 'edit_hint.php?id='.htmlspecialchars($_POST['id']).'&generic_success=1');
    }

    else if ($_POST['action'] == 'delete') {

        if (!$_POST['delete_confirmation']) {
            message_error('Please confirm delete');
        }

        db_delete(
            'hints',
            array(
                'id'=>$_POST['id']
            )
        );

        invalidate_cache('hints');

        redirect(CONFIG_SITE_ADMIN_RELPATH . 'list_hints.php?generic_success=1');
    }
}