<?php

require('../../../include/mellivora.inc.php');

enforce_authentication(CONST_USER_CLASS_MODERATOR);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    validate_xsrf_token($_POST[CONST_XSRF_TOKEN_KEY]);

    if ($_POST['action'] == 'new') {

        $id = db_insert(
           'dynamic_pages',
           array(
              'title'=>$_POST['title'],
              'body'=>$_POST['body'],
              'visibility'=>$_POST['visibility'],
              'min_user_class'=>$_POST['min_user_class']
           )
        );

        redirect(CONFIG_SITE_ADMIN_RELPATH . 'edit_dynamic_page.php?id='.$id.'&generic_success=1');
    }
}