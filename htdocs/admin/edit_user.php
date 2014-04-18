<?php

require('../../include/mellivora.inc.php');

enforce_authentication(CONFIG_UC_MODERATOR);

validate_id($_GET['id']);

$stmt = $db->prepare('SELECT * FROM users WHERE id = :id');
$stmt->execute(array(':id' => $_GET['id']));
$user = $stmt->fetch(PDO::FETCH_ASSOC);

head('Site management');
menu_management();

section_subhead('Edit user: ' . $user['team_name']);

form_start('edit_user');
form_input_text('Email', $user['email']);
form_input_text('Team name', $user['team_name']);
form_input_checkbox('Enabled', $user['enabled']);
form_hidden('action', 'edit');
form_hidden('id', $_GET['id']);
form_button_submit('Save changes');
form_end();

section_subhead('Reset password');
form_start();
form_input_checkbox('Reset confirmation');
form_hidden('action', 'reset_password');
form_hidden('id', $_GET['id']);
form_button_submit('Reset password', 'warning');
form_end();

section_subhead('Delete user');
form_start();
form_input_checkbox('Delete confirmation');
form_hidden('action', 'delete');
form_hidden('id', $_GET['id']);
message_inline_warning('Warning! This will delete all submissions made by this user!');
form_button_submit('Delete user', 'danger');

foot();