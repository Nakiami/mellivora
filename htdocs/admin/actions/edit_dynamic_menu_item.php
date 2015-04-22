<?php

require('../../../include/mellivora.inc.php');

enforce_authentication(CONST_USER_CLASS_MODERATOR);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    validate_id($_POST['id']);
    validate_xsrf_token($_POST[CONST_XSRF_TOKEN_KEY]);

    if ($_POST['action'] == 'edit') {

        db_update(
           'dynamic_menu',
           array(
              'title'=>$_POST['title'],
              'permalink'=>$_POST['permalink'],
              'url'=>$_POST['url'],
              'visibility'=>$_POST['visibility'],
              'min_user_class'=>$_POST['min_user_class'],
              'priority'=>$_POST['priority'],
              'internal_page'=>$_POST['internal_page']
           ),
           array(
              'id'=>$_POST['id']
           )
        );

        invalidate_cache_group(CONST_CACHE_GROUP_NAME_DYNAMIC_MENU);

        redirect(CONFIG_SITE_ADMIN_RELPATH . 'edit_dynamic_menu_item.php?id='.$_POST['id'].'&generic_success=1');
    }

    else if ($_POST['action'] == 'delete') {

        if (!$_POST['delete_confirmation']) {
            message_error('Please confirm delete');
        }

        db_delete(
            'dynamic_menu',
            array(
                'id'=>$_POST['id']
            )
        );

        invalidate_cache_group(CONST_CACHE_GROUP_NAME_DYNAMIC_MENU);

        redirect(CONFIG_SITE_ADMIN_RELPATH . 'list_dynamic_menu.php?generic_success=1');
    }
}