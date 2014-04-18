<?php

require('../../include/mellivora.inc.php');

enforce_authentication(CONFIG_UC_MODERATOR);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    validate_id($_POST['id']);

    if ($_POST['action'] == 'edit') {

       db_update(
          'restrict_email',
          array(
             'rule'=>$_POST['rule'],
             'enabled'=>$_POST['enabled'],
             'white'=>$_POST['whitelist'],
             'priority'=>$_POST['priority']
          ),
          array(
             'id'=>$_POST['id']
          )
       );

        redirect('list_restrict_email.php?generic_success=1');
    }

    else if ($_POST['action'] == 'delete') {

        if (!$_POST['delete_confirmation']) {
            message_error('Please confirm delete');
        }

        db_delete(
            'restrict_email',
            array(
                'id'=>$_POST['id']
            )
        );

        redirect('list_restrict_email.php?generic_success=1');
    }
}