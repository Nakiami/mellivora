<?php

require('../../../include/mellivora.inc.php');

enforce_authentication(CONFIG_UC_MODERATOR);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if ($_POST['action'] == 'new') {

       $id = db_insert(
          'challenges',
          array(
             'added'=>time(),
             'added_by'=>$_SESSION['id'],
             'title'=>$_POST['title'],
             'description'=>$_POST['description'],
             'flag'=>$_POST['flag'],
             'automark'=>$_POST['automark'],
             'case_insensitive'=>$_POST['case_insensitive'],
             'points'=>$_POST['points'],
             'category'=>$_POST['category'],
             'num_attempts_allowed'=>$_POST['num_attempts_allowed'],
             'available_from'=>strtotime($_POST['available_from']),
             'available_until'=>strtotime($_POST['available_until'])
          )
       );

       if ($id) {
          redirect(CONFIG_SITE_ADMIN_RELPATH . 'edit_challenge.php?id='.$id);
       } else {
          message_error('Could not insert new challenge.');
       }
    }
}