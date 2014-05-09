<?php

require('../../include/mellivora.inc.php');

prefer_ssl();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($_POST['action'] == 'register') {

        if (CONFIG_RECAPTCHA_ENABLE_PUBLIC) {
            validate_captcha();
        }

        validate_email($_POST['email']);

        $interest = db_select_one(
            'interest',
            array('id'),
            array('email' => $_POST['email'])
        );

        if ($interest['id']) {
            message_error('You have already registered your interest!');
        }

        $id = db_insert(
            'interest',
            array(
                'added'=>time(),
                'name'=>$_POST['name'],
                'email'=>$_POST['email'],
                'secret'=>generate_random_string(40)
            )
        );

        if ($id) {
            message_generic('Success', 'The email '.htmlspecialchars($_POST['email']).' has been registered. We look forward to seeing you in our next competition!');
        } else {
            message_error('Could not register interest. You must not be interested enough!');
        }
    }
}