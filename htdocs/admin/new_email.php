<?php

require('../../include/mellivora.inc.php');

enforce_authentication(CONFIG_UC_MODERATOR);

head('Site management');
menu_management();

if (array_get($_GET, 'bcc') == 'all') {
    $users = db_select_all(
        'users',
        array('email')
    );

    $bcc = '';
    foreach ($users as $user) {
        $bcc .= $user['email'].",\n";
    }
    $bcc = trim($bcc);
}

section_subhead('New email');

message_inline_blue('Separate receiver emails with a comma and optional whitespace. You can use BBCode. If you do, you must send as HTML email.');

form_start(CONFIG_SITE_ADMIN_RELPATH . 'actions/new_email');

if(isset($bcc)) {
    form_input_text('To', CONFIG_EMAIL_FROM_EMAIL);
    form_input_text('CC');
    form_textarea('BCC', $bcc);
} else {
    form_input_text('To', isset($_GET['to']) ? $_GET['to'] : '');
    form_input_text('CC');
    form_input_text('BCC');
}

form_input_text('Subject');
form_textarea('Body');

form_input_checkbox('HTML email');

form_hidden('action', 'new');

message_inline_yellow('Important email? Remember to Ctrl+C before attempting to send!');

form_button_submit('Send email');
form_end();

foot();