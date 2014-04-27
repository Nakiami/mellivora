<?php

require('../../../include/mellivora.inc.php');

enforce_authentication(CONFIG_UC_MODERATOR);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    validate_xsrf_token($_POST['xsrf_token']);

    if ($_POST['action'] == 'new') {

        $id = db_insert(
          'user_types',
          array(
             'title'=>$_POST['title'],
             'description'=>$_POST['description']
          )
        );

        if ($id) {
            redirect(CONFIG_SITE_ADMIN_RELPATH . 'edit_user_type.php?id='.$id);
        } else {
            message_error('Could not insert new user type.');
        }
    }
}