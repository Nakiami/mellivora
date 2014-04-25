<?php

require('../../include/mellivora.inc.php');

enforce_authentication();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if ($_POST['action'] == 'edit') {

        db_update(
          'users',
          array(
             'country_id'=>$_POST['country']
          ),
          array(
             'id'=>$_SESSION['id']
          )
        );

        redirect('profile?generic_success=1');
    }

    else if ($_POST['action'] == 'reset_password') {

        if (!strlen($_POST['password'])) {
            message_error('Password cannot be empty.');
        }

        if ($_POST['password'] != $_POST['password_again']) {
            message_error('Passwords did not match.');
        }

        $new_passhash = make_passhash($_POST['password']);

        $password_set = db_update(
            'users',
            array(
                'passhash'=>$new_passhash
            ),
            array(
                'id'=>$_SESSION['id']
            )
        );

        if (!$password_set) {
            message_error('Password not set.');
        }

        redirect('profile?generic_success=1');
    }
}