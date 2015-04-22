<?php

require('../../../include/mellivora.inc.php');

enforce_authentication(CONST_USER_CLASS_MODERATOR);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    validate_xsrf_token($_POST[CONST_XSRF_TOKEN_KEY]);

    if ($_POST['action'] == 'new') {

        $id = db_insert(
          'hints',
          array(
             'added'=>time(),
             'added_by'=>$_SESSION['id'],
             'challenge'=>$_POST['challenge'],
             'visible'=>$_POST['visible'],
             'body'=>$_POST['body']
          )
        );

        if ($id) {
            invalidate_cache(CONST_CACHE_NAME_HINTS);

            redirect(CONFIG_SITE_ADMIN_RELPATH . 'edit_hint.php?id='.$id);
        } else {
            message_error('Could not insert new hint.');
        }
    }
}