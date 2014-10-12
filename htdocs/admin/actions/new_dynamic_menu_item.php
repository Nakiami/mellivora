<?php

require('../../../include/mellivora.inc.php');

enforce_authentication(CONFIG_UC_MODERATOR);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    validate_xsrf_token($_POST['xsrf_token']);

    if ($_POST['action'] == 'new') {

        $id = db_insert(
           'dynamic_menu',
           array(
              'title'=>$_POST['title'],
              'permalink'=>$_POST['permalink'],
              'url'=>$_POST['url'],
              'visibility'=>$_POST['visibility'],
              'min_user_class'=>$_POST['min_user_class'],
              'priority'=>$_POST['priority'],
              'internal_page'=>$_POST['internal_page']
           )
        );

        invalidate_cache_group('dynamic_menu');

        redirect(CONFIG_SITE_ADMIN_RELPATH . 'edit_dynamic_menu_item.php?id='.$id.'&generic_success=1');
    }
}