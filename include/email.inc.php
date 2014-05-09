<?php

function send_email (
    array $receivers,
    $subject,
    $body,
    array $cc_list = null,
    array $bcc_list = null,
    $from_email = CONFIG_EMAIL_FROM_EMAIL,
    $from_name = CONFIG_EMAIL_FROM_NAME,
    $replyto_email = CONFIG_EMAIL_REPLYTO_EMAIL,
    $replyto_name = CONFIG_EMAIL_REPLYTO_NAME,
    $is_html = false) {

    $mail = new PHPMailer();
    $mail->IsHTML($is_html);

    $successfully_sent_to = array();

    try {

        if (CONFIG_EMAIL_USE_SMTP) {
            $mail->IsSMTP();

            $mail->SMTPDebug = CONFIG_EMAIL_SMTP_DEBUG_LEVEL;

            $mail->Host = CONFIG_EMAIL_SMTP_HOST;
            $mail->Port = CONFIG_EMAIL_SMTP_PORT;
            $mail->SMTPSecure = CONFIG_EMAIL_SMTP_SECURITY;

            $mail->SMTPAuth = CONFIG_EMAIL_SMTP_AUTH;
            $mail->Username = CONFIG_EMAIL_SMTP_USER;
            $mail->Password = CONFIG_EMAIL_SMTP_PASSWORD;
        }

        $mail->SetFrom($from_email, $from_name);
        if ($replyto_email) {
            $mail->AddReplyTo($replyto_email, $replyto_name);
        }

        // add the "To" receivers
        foreach ($receivers as $receiver) {
            if (!valid_email($receiver)) {
                continue;
            }
            $mail->AddAddress($receiver);
            $successfully_sent_to[] = $receiver;
        }

        if (empty($successfully_sent_to)) {
            message_error('There must be at least one valid "To" receiver of this email');
        }

        // add the "CC" receivers
        if (!empty($cc_list)) {
            foreach ($cc_list as $cc) {
                if (!valid_email($cc)) {
                    continue;
                }
                $mail->AddCC($cc);
                $successfully_sent_to[] = $cc;
            }
        }

        // add the "BCC" receivers
        if (!empty($bcc_list)) {
            foreach ($bcc_list as $bcc) {
                if (!valid_email($bcc)) {
                    continue;
                }
                $mail->AddBCC($bcc);
                $successfully_sent_to[] = $bcc;
            }
        }

        $mail->Subject = $subject;

        // HTML email
        if ($is_html) {
            require(CONFIG_PATH_THIRDPARTY . 'nbbc/nbbc.php');

            $bbc = new BBCode();
            $bbc->SetEnableSmileys(false);

            // we assume the email has come to us in BBCode format
            $mail->MsgHTML($bbc->parse($body));
        }

        // plain old simple email
        else {
            $mail->Body = $body;
        }

        if(!$mail->Send()) {
            throw new Exception('Could not send email: ' . $mail->ErrorInfo);
        }

    } catch (Exception $e) {
        log_exception($e);
        message_error('Could not send email! An exception has been logged. Please contact '.(CONFIG_EMAIL_REPLYTO_EMAIL ? CONFIG_EMAIL_REPLYTO_EMAIL : CONFIG_EMAIL_FROM_EMAIL));
    }

    return $successfully_sent_to;
}

function allowed_email ($email) {
    $allowedEmail = true;

    $rules = db_select_all(
        'restrict_email',
        array(
            'rule',
            'white'
        ),
        array(
            'enabled'=>1
        ),
        'priority ASC'
    );

    foreach($rules as $rule) {
        if (preg_match('/'.$rule['rule'].'/', $email)) {
            if ($rule['white']) {
                $allowedEmail = true;
            } else {
                $allowedEmail = false;
            }
        }
    }

    return $allowedEmail;
}

function valid_email ($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

function validate_email ($email) {
    if (!valid_email($email)) {
        log_exception(new Exception('Invalid Email'));

        message_error('That doesn\'t look like an email. Please go back and double check the form.');
    }
}

function csv_email_list_to_array ($list) {
    return array_map('trim', str_getcsv($list));
}