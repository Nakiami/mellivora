<?php

require('../include/mellivora.inc.php');

// get auth data
if (isset($_GET['auth_key']) && is_valid_id($_GET['id'])) {

    $auth = db_select_one(
        'reset_password',
        array(
            'id',
            'user_id',
            'auth_key'
        ),
        array(
            'auth_key' => $_GET['auth_key'],
            'user_id' => $_GET['id']
        )
    );

    if (!$auth['user_id']) {
        message_error('No reset data found');
    }
}

// start here
if (!isset($_GET['action'])) {

    head('Reset password');
    echo '
    <form method="post" class="form-signin" action="actions/reset_password">
        <h2 class="form-signin-heading">Reset password</h2>
        <input name="',md5(CONFIG_SITE_NAME.'EMAIL'),'" type="text" class="form-control" placeholder="Email address" required autofocus />
        <input type="hidden" name="action" value="reset_password" />
        <button class="btn btn-primary" type="submit">Reset password</button>
    </form>
    ';
    foot();
}

// return from password reset email here
else if ($_GET['action']=='choose_password' && is_valid_id($auth['user_id'])) {

    head('Choose password');
    echo '
    <form method="post" class="form-signin" action="actions/reset_password">
        <h2 class="form-signin-heading">Choose password</h2>
        <input name="',md5(CONFIG_SITE_NAME.'PWD'),'" type="password" class="form-control" placeholder="Password" required autofocus />
        <input type="hidden" name="action" value="choose_password" />
        <input type="hidden" name="auth_key" value="',htmlspecialchars($_GET['auth_key']),'" />
        <input type="hidden" name="id" value="',htmlspecialchars($_GET['id']),'" />
        <button class="btn btn-primary" type="submit">Reset password</button>
    </form>
    ';
    foot();
}