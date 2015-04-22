<?php

require('../../../include/mellivora.inc.php');

enforce_authentication(CONST_USER_CLASS_MODERATOR);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    validate_id($_POST['id']);
    validate_xsrf_token($_POST[CONST_XSRF_TOKEN_KEY]);

    $challenge = db_select_one(
        'hints',
        array(
            'challenge AS id'
        ),
        array(
            'id'=>$_POST['id']
        )
    );

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

        invalidate_cache(CONST_CACHE_NAME_HINTS);
        invalidate_cache(CONST_CACHE_NAME_CHALLENGE_HINTS . $challenge['id']);

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

        invalidate_cache(CONST_CACHE_NAME_HINTS);
        invalidate_cache(CONST_CACHE_NAME_CHALLENGE_HINTS . $challenge['id']);

        redirect(CONFIG_SITE_ADMIN_RELPATH . 'list_hints.php?generic_success=1');
    }
}