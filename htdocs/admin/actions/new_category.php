<?php

require('../../../include/mellivora.inc.php');

enforce_authentication(CONFIG_UC_MODERATOR);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if ($_POST['action'] == 'new') {

       $id = db_insert(
          'categories',
          array(
             'added'=>time(),
             'added_by'=>$_SESSION['id'],
             'title'=>$_POST['title'],
             'description'=>$_POST['description'],
             'available_from'=>strtotime($_POST['available_from']),
             'available_until'=>strtotime($_POST['available_until'])
          )
       );

        if ($id) {
            redirect(CONFIG_SITE_ADMIN_RELPATH . 'edit_category.php?id='.$id);
        } else {
            message_error('Could not insert new category: '.$db->errorCode());
        }
    }
}