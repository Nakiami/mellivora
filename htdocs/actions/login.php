<?php

require('../../include/mellivora.inc.php');

$redirect_url = array_get($_POST, 'redirect');

if (user_is_logged_in()) {
    redirect($redirect_url);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($_POST['action'] == 'login') {

        $email = $_POST[md5(CONFIG_SITE_NAME.'USR')];
        $password = $_POST[md5(CONFIG_SITE_NAME.'PWD')];
        $remember_me = isset($_POST['remember_me']);

        if (login_create($email, $password, $remember_me)) {
            enforce_2fa();
            redirect($redirect_url);
        } else {
            message_error('Login failed? Helpful.');
        }
    }
}