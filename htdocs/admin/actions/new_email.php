<?php

require('../../../include/mellivora.inc.php');

enforce_authentication(CONFIG_UC_MODERATOR);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    validate_xsrf_token($_POST['xsrf_token']);

    if ($_POST['action'] == 'new') {
        $successfully_sent_to = send_email(
            csv_email_list_to_array($_POST['to']),
            $_POST['subject'],
            $_POST['body'],
            csv_email_list_to_array($_POST['cc']),
            csv_email_list_to_array($_POST['bcc']),
            CONFIG_EMAIL_FROM_EMAIL,
            CONFIG_EMAIL_FROM_NAME,
            CONFIG_EMAIL_REPLYTO_EMAIL,
            CONFIG_EMAIL_REPLYTO_NAME,
            (isset($_POST['html_email']) ? true : false)
        );

        message_generic(
            'Status',
            'Successfully sent emails to '.count($successfully_sent_to).' addresses.
            List: ' . implode(', ', $successfully_sent_to)
        );
    }
}