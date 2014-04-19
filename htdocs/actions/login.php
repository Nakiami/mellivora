<?php

require('../../include/mellivora.inc.php');

if (user_is_logged_in()) {
    redirect(CONFIG_LOGIN_REDIRECT_TO);
}

prefer_ssl();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($_POST['action'] == 'login') {
        if (login_session_create($_POST)) {
            redirect(CONFIG_LOGIN_REDIRECT_TO);
        } else {
            message_error('Login failed? Helpful.');
        }
    }

    else if ($_POST['action'] == 'register') {

        if (CONFIG_RECAPTCHA_ENABLE) {
            check_captcha($_POST);
        }

        if (register_account($_POST) && login_session_create($_POST)) {
            redirect(CONFIG_REGISTER_REDIRECT_TO);
        } else {
            message_error('Sign up failed? Helpful.');
        }
    }
}