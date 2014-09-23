<?php

require('../../include/mellivora.inc.php');

$redirect_url = get_file_name($_POST['redirect'] ? $_POST['redirect'] : CONFIG_LOGIN_REDIRECT_TO);

if (user_is_logged_in()) {
    redirect($redirect_url);
}

prefer_ssl();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($_POST['action'] == 'register') {

        if (CONFIG_RECAPTCHA_ENABLE_PUBLIC) {
            validate_captcha();
        }

        $email = $_POST[md5(CONFIG_SITE_NAME.'USR')];

        if (CONFIG_ACCOUNTS_EMAIL_PASSWORD_ON_SIGNUP) {
            $password = generate_random_string(12);
        } else {
            $password = $_POST[md5(CONFIG_SITE_NAME.'PWD')];
        }

        echo register_account(
            $email,
            $password,
            $_POST['team_name'],
            $_POST['country'],
            $_POST['type']
        );
    }
}