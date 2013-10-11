<?php

require('../include/general.inc.php');

enforce_authentication(CONFIG_UC_MODERATOR);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if ($_POST['action'] == 'test') {

        if (pass_email_whitelist($_POST['email'])) {
            message_generic('Yes', 'A user will be able to sign up with this email.');
        } else {
            message_generic('No', 'A user will NOT be able to sign up with this email.');
        }
    }
}

head('Site management');
menu_management();

section_subhead('Test signup rules');

message_inline_info('Enter an email addess to test.');

form_start();
form_input_text('Email');
form_hidden('action', 'test');
form_button_submit('Test');
form_end();

foot();