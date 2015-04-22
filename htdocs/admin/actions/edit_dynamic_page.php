<?php

require('../../../include/mellivora.inc.php');

enforce_authentication(CONST_USER_CLASS_MODERATOR);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    validate_id($_POST['id']);
    validate_xsrf_token($_POST[CONST_XSRF_TOKEN_KEY]);

    if ($_POST['action'] == 'edit') {

        db_update(
           'dynamic_pages',
           array(
              'title'=>$_POST['title'],
              'body'=>$_POST['body'],
              'visibility'=>$_POST['visibility'],
              'min_user_class'=>$_POST['min_user_class']
           ),
           array(
              'id'=>$_POST['id']
           )
        );

        invalidate_cache($_POST['id'], CONST_CACHE_DYNAMIC_PAGES_GROUP);

        redirect(CONFIG_SITE_ADMIN_RELPATH . 'edit_dynamic_page.php?id='.$_POST['id'].'&generic_success=1');
    }

    else if ($_POST['action'] == 'delete') {

        if (!$_POST['delete_confirmation']) {
            message_error('Please confirm delete');
        }

        db_delete(
            'dynamic_pages',
            array(
                'id'=>$_POST['id']
            )
        );

        invalidate_cache($_POST['id'], CONST_CACHE_DYNAMIC_PAGES_GROUP);

        redirect(CONFIG_SITE_ADMIN_RELPATH . 'list_dynamic_pages.php?generic_success=1');
    }
}