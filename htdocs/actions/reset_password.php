<?php

require('../../include/mellivora.inc.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // get auth data
    if (isset($_POST['auth_key']) && is_valid_id($_POST['id'])) {

        $auth = db_select_one(
            'reset_password',
            array(
                'id',
                'user_id',
                'auth_key'
            ),
            array(
                'auth_key' => $_POST['auth_key'],
                'user_id' => $_POST['id']
            )
        );

        if (!$auth['user_id']) {
            message_error('No reset data found');
        }
    }

    // stage 1, part 2
    if ($_POST['action'] == 'reset_password') {

        $user = db_select_one(
            'users',
            array(
                'id',
                'team_name',
                'email'
            ),
            array(
                'email' => $_POST[md5(CONFIG_SITE_NAME . 'EMAIL')]
            )
        );

        if ($user['id']) {

            $auth_key = hash('sha256', generate_random_string(128));

            db_insert(
                'reset_password',
                array(
                    'added'=>time(),
                    'user_id'=>$user['id'],
                    'ip'=>get_ip(true),
                    'auth_key'=>$auth_key
                )
            );

            $email_subject = 'Password recovery for team ' . htmlspecialchars($user['team_name']);
            // body
            $email_body = htmlspecialchars($user['team_name']).', please follow the link below to reset your password:'.
                "\r\n".
                "\r\n".
                CONFIG_SITE_URL . 'reset_password?action=choose_password&auth_key='.$auth_key.'&id='.$user['id'].
                "\r\n".
                "\r\n".
                'Regards,'.
                "\r\n".
                CONFIG_SITE_NAME;

            // send details to user
            send_email(array($user['email']), $email_subject, $email_body);
        }

        message_generic('Success', 'If the email you provided was found in the database, an email has now been sent to it with further instructions!');
    }

    // stage 2, part 2
    else if ($_POST['action'] == 'choose_password' && is_valid_id($auth['user_id'])) {

        $new_password = $_POST[md5(CONFIG_SITE_NAME.'PWD')];

        if (empty($new_password)) {
            message_error('You can\'t have an empty password');
        }

        $new_passhash = make_passhash($new_password);

        db_update(
            'users',
            array(
                'passhash'=>$new_passhash
            ),
            array(
                'id'=>$auth['user_id']
            )
        );

        db_delete(
            'reset_password',
            array(
                'id'=>$auth['id']
            )
        );

        message_generic('Success', 'Your password has been reset.');
    }
}