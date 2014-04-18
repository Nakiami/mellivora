<?php

require('../../../include/mellivora.inc.php');

enforce_authentication(CONFIG_UC_MODERATOR);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    validate_id($_POST['id']);

    if ($_POST['action'] == 'edit') {

        validate_email($_POST['email']);

        db_update(
          'users',
          array(
             'email'=>$_POST['email'],
             'team_name'=>$_POST['team_name'],
             'enabled'=>$_POST['enabled']
          ),
          array(
             'id'=>$_POST['id']
          )
        );

        redirect(CONFIG_SITE_ADMIN_RELPATH . 'list_users.php?generic_success=1');
    }

    else if ($_POST['action'] == 'delete') {

        if (!$_POST['delete_confirmation']) {
            message_error('Please confirm delete');
        }

        db_delete(
            'users',
            array(
                'id'=>$_POST['id']
            )
        );

        db_delete(
            'submissions',
            array(
                'user_id'=>$_POST['id']
            )
        );

        db_delete(
            'ip_log',
            array(
                'user_id'=>$_POST['id']
            )
        );

        redirect(CONFIG_SITE_ADMIN_RELPATH . 'list_users.php?generic_success=1');
    }

    else if ($_POST['action'] == 'reset_password') {
        $new_password = generate_random_string(8, false);
        $new_salt = make_salt();

        $new_passhash = make_passhash($new_password, $new_salt);

        db_update(
            'users',
            array(
                'salt'=>$new_salt,
                'passhash'=>$new_passhash
            ),
            array(
                'id'=>$_POST['id']
            )
        );

        message_generic('Success', 'Users new password is: ' . $new_password);
    }
}