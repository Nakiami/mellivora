<?php

require('../../../include/mellivora.inc.php');

enforce_authentication(CONFIG_UC_MODERATOR);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

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
            invalidate_cache('hints');

            redirect(CONFIG_SITE_ADMIN_RELPATH . 'edit_hint.php?id='.$id);
        } else {
            message_error('Could not insert new hint: '.$db->errorCode());
        }
    }
}