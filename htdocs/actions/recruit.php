<?php

require('../../include/mellivora.inc.php');

prefer_ssl();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($_POST['action'] == 'register') {

        if (CONFIG_RECAPTCHA_ENABLE_PRIVATE) {
            validate_captcha();
        }

        validate_email($_POST['email']);

        $recruit = db_select_one(
            'recruit',
            array('id'),
            array('email' => $_POST['email'])
        );

        if ($recruit['id']) {
            message_generic('Thank you', 'Your email was already registered!');
        }

        $id = db_insert(
            'recruit',
            array(
                'added'=>time(),
                'user_id'=>$_SESSION['id'],
                'name'=>$_POST['name'],
                'email'=>$_POST['email'],
                'city'=>$_POST['city'],
                'country'=>$_POST['country']
            )
        );

        if ($id) {
            message_generic('Success', 'The email '.htmlspecialchars($_POST['email']).' has been registered. Thanks!');
        } else {
            message_error('Could not register interest. You must not be interested enough!');
        }
    }
}