<?php

require('../../include/mellivora.inc.php');

prefer_ssl();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($_POST['action'] == 'authenticate') {
        if (validate_two_factor_auth_code($_POST['code'])) {
            session_set_2fa_authenticated();
            redirect(CONFIG_LOGIN_REDIRECT_TO);
        } else {
            redirect('two_factor_auth?generic_failure=1');
        }
    }
}