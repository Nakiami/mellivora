<?php

require('../include/general.inc.php');

enforce_authentication(CONFIG_UC_MODERATOR);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    validate_id($_POST['id']);

    if ($_POST['action'] == 'edit') {

        validate_email($_POST['email']);

        db_update(
          'users',
          array(
             'email'=>$_POST['email'],
             'team_name'=>$_POST['team_name'],
             'enabled'=>($_POST['enabled'] ? 1 : 0)
          ),
          array(
             'id'=>$_POST['id']
          )
        );

        header('location: list_users.php?generic_success=1');
        exit();
    }

    else if ($_POST['action'] == 'delete') {

        if (!$_POST['delete_confirmation']) {
            message_error('Please confirm delete');
        }

        $stmt = $db->prepare('DELETE FROM users WHERE id=:id');
        $stmt->execute(array(':id'=>$_POST['id']));

        $stmt = $db->prepare('DELETE FROM submissions WHERE user_id=:id');
        $stmt->execute(array(':id'=>$_POST['id']));

        $stmt = $db->prepare('DELETE FROM ip_log WHERE user_id=:id');
        $stmt->execute(array(':id'=>$_POST['id']));

        header('location: list_users.php?generic_success=1');
        exit();
    }

    else if ($_POST['action'] == 'reset_password') {
        $new_password = generate_random_string(8, false);
        $new_salt = make_salt();

        $new_passhash = make_passhash($new_password, $new_salt);

        $stmt = $db->prepare('
        UPDATE users SET
        salt=:salt,
        passhash=:passhash
        WHERE id=:id
        ');
        $stmt->execute(array(':passhash'=>$new_passhash, ':salt'=>$new_salt, ':id'=>$_POST['id']));

        message_generic('Success', 'Users new password is: ' . $new_password);
    }
}

validate_id($_GET['id']);

$stmt = $db->prepare('SELECT * FROM users WHERE id = :id');
$stmt->execute(array(':id' => $_GET['id']));
$user = $stmt->fetch(PDO::FETCH_ASSOC);

head('Site management');
menu_management();

section_subhead('Edit user: ' . $user['team_name']);

form_start();
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