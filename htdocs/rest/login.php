<?php

require('../../include/mellivora.inc.php');

if (user_is_logged_in()) {
    echo json_error('already logged in');
}

prefer_ssl();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($_POST['action'] == 'login') {

        $email = $_POST[md5(CONFIG_SITE_NAME.'USR')];
        $password = $_POST[md5(CONFIG_SITE_NAME.'PWD')];
        $remember_me = isset($_POST['remember_me']);

        echo login_create($email, $password, $remember_me);
    }
}