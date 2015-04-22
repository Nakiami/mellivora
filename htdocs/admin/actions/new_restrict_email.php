<?php

require('../../../include/mellivora.inc.php');

enforce_authentication(CONST_USER_CLASS_MODERATOR);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    validate_xsrf_token($_POST[CONST_XSRF_TOKEN_KEY]);

    if ($_POST['action'] == 'new') {

       $id = db_insert(
          'restrict_email',
          array(
             'added'=>time(),
             'added_by'=>$_SESSION['id'],
             'rule'=>$_POST['rule'],
             'white'=>$_POST['whitelist'],
             'priority'=>$_POST['priority'],
             'enabled'=>$_POST['enabled']
          )
       );

       if ($id) {
          redirect(CONFIG_SITE_ADMIN_RELPATH . 'list_restrict_email.php?generic_success=1');
       } else {
          message_error('Could not insert new email restriction.');
       }
    }
}