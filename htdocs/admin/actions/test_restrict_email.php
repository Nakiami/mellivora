<?php

require('../../../include/mellivora.inc.php');

enforce_authentication(CONFIG_UC_MODERATOR);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    validate_xsrf_token($_POST['xsrf_token']);

    if ($_POST['action'] == 'test') {

        if (allowed_email($_POST['email'])) {
            message_generic('Yes', 'A user will be able to sign up with this email.');
        } else {
            message_generic('No', 'A user will NOT be able to sign up with this email.');
        }
    }
}