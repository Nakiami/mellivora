<?php

require('../include/mellivora.inc.php');

// get auth data
if (isset($_GET['auth_key']) && valid_id($_GET['id']) || isset($_POST['auth_key']) && valid_id($_POST['id'])) {

    $auth_key = $_GET['auth_key'] ? $_GET['auth_key'] : $_POST['auth_key'];
    $user_id = $_GET['id'] ? $_GET['id'] : $_POST['id'];

    $stmt = $db->prepare('SELECT id, user_id, auth_key FROM reset_password WHERE auth_key = :auth_key AND user_id = :user_id');
    $stmt->execute(array(':auth_key'=>$auth_key, ':user_id' => $user_id));
    $auth = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$auth['user_id']) {
        message_error('No reset data found');
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // stage 1, part 2
    if ($_POST['action'] == 'reset_password') {

        $stmt = $db->prepare('SELECT id, team_name FROM users WHERE email = :email');
        $stmt->execute(array(':email' => $_POST[md5(CONFIG_SITE_NAME.'EMAIL')]));
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

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
            send_email($user['email'], $user['team_name'], $email_subject, $email_body);
        }

        message_generic('Success', 'If the email you provided was found in the database, an email has now been sent to it with further instructions!');
    }

    // stage 2, part 2
    else if ($_POST['action'] == 'choose_password' && valid_id($auth['user_id'])) {

        $new_password = $_POST[md5(CONFIG_SITE_NAME.'PWD')];

        if (empty($new_password)) {
            message_error('You can\'t have an empty password');
        }

        $new_salt = make_salt();
        $new_passhash = make_passhash($new_password, $new_salt);

        db_update(
            'users',
            array(
                'salt'=>$new_salt,
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

// stage 2, part 1
else if ($_GET['action'] == 'choose_password' && valid_id($auth['user_id'])) {

    head('Choose password');

    echo '
    <form method="post" class="form-signin">
        <h2 class="form-signin-heading">Choose password</h2>
        <input name="',md5(CONFIG_SITE_NAME.'PWD'),'" type="password" class="input-block-level" placeholder="Password">
        <input type="hidden" name="action" value="choose_password" />
        <input type="hidden" name="id" value="',htmlspecialchars($user['id']),'" />
        <input type="hidden" name="auth_key" value="',htmlspecialchars($user['auth_key']),'" />
        <button class="btn btn-primary" type="submit">Reset password</button>
    </form>
    ';
}

// stage 1, part 1
else {
    head('Reset password');
    echo '
    <form method="post" class="form-signin">
        <h2 class="form-signin-heading">Reset password</h2>
        <input name="',md5(CONFIG_SITE_NAME.'EMAIL'),'" type="text" class="input-block-level" placeholder="Email address">
        <input type="hidden" name="action" value="reset_password" />
        <button class="btn btn-primary" type="submit">Reset password</button>
    </form>
    ';
}