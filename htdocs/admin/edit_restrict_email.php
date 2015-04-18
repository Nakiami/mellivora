<?php

require('../../include/mellivora.inc.php');

enforce_authentication(CONST_USER_CLASS_MODERATOR);

validate_id($_GET['id']);

$rule = db_select_one(
    'restrict_email',
    array(
        'rule',
        'enabled',
        'white',
        'priority'
    ),
    array('id' => $_GET['id'])
);

head('Site management');
menu_management();

section_subhead('Edit signup rule');
form_start(CONFIG_SITE_ADMIN_RELPATH . 'actions/edit_restrict_email');
form_input_text('Rule', $rule['rule']);
form_input_text('Priority', $rule['priority']);
form_input_checkbox('Whitelist', $rule['white']);
form_input_checkbox('Enabled', $rule['enabled']);
form_hidden('action', 'edit');
form_hidden('id', $_GET['id']);
form_button_submit('Save changes');
form_end();

section_subhead('Delete rule');
form_start(CONFIG_SITE_ADMIN_RELPATH . 'actions/edit_restrict_email');
form_input_checkbox('Delete confirmation');
form_hidden('action', 'delete');
form_hidden('id', $_GET['id']);
form_button_submit('Delete rule', 'danger');
form_end();

foot();