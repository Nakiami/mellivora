<?php

require('../../include/mellivora.inc.php');

enforce_authentication(CONFIG_UC_MODERATOR);

validate_id($_GET['id']);

$user = db_select(
    'users',
    array(
        'team_name',
        'email',
        'enabled',
        'competing'
    ),
    array('id'=>$_GET['id']),
    false
);

head('Site management');
menu_management();

section_subhead('Edit user: ' . $user['team_name']);

form_start(CONFIG_SITE_ADMIN_RELPATH . 'actions/edit_user');
form_input_text('Email', $user['email']);
form_input_text('Team name', $user['team_name']);
form_input_checkbox('Enabled', $user['enabled']);
form_input_checkbox('Competing', $user['competing']);
form_hidden('action', 'edit');
form_hidden('id', $_GET['id']);
form_button_submit('Save changes');
form_end();

section_subhead('Reset password');
form_start(CONFIG_SITE_ADMIN_RELPATH . 'actions/edit_user');
form_input_checkbox('Reset confirmation');
form_hidden('action', 'reset_password');
form_hidden('id', $_GET['id']);
form_button_submit('Reset password', 'warning');
form_end();

section_subhead('Delete user');
form_start(CONFIG_SITE_ADMIN_RELPATH . 'actions/edit_user');
form_input_checkbox('Delete confirmation');
form_hidden('action', 'delete');
form_hidden('id', $_GET['id']);
message_inline_red('Warning! This will delete all submissions made by this user!');
form_button_submit('Delete user', 'danger');

foot();