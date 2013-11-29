<?php

require('../include/mellivora.inc.php');

enforce_authentication(CONFIG_UC_MODERATOR);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    validate_id($_POST['id']);

    if ($_POST['action'] == 'edit') {

       db_update(
          'restrict_email',
          array(
             'rule'=>$_POST['rule'],
             'enabled'=>$_POST['enabled'],
             'white'=>$_POST['whitelist'],
             'priority'=>$_POST['priority']
          ),
          array(
             'id'=>$_POST['id']
          )
       );

        redirect('list_restrict_email.php?generic_success=1');
    }

    else if ($_POST['action'] == 'delete') {

        if (!$_POST['delete_confirmation']) {
            message_error('Please confirm delete');
        }

        db_delete(
            'restrict_email',
            array(
                'id'=>$_POST['id']
            )
        );

        redirect('list_restrict_email.php?generic_success=1');
    }
}

validate_id($_GET['id']);

$stmt = $db->prepare('SELECT rule, enabled, white, priority FROM restrict_email WHERE id = :id');
$stmt->execute(array(':id' => $_GET['id']));
$rule = $stmt->fetch(PDO::FETCH_ASSOC);

head('Site management');
menu_management();

section_subhead('Edit signup rule');
form_start();
form_input_text('Rule', $rule['rule']);
form_input_text('Priority', $rule['priority']);
form_input_checkbox('Whitelist', $rule['white']);
form_input_checkbox('Enabled', $rule['enabled']);
form_hidden('action', 'edit');
form_hidden('id', $_GET['id']);
form_button_submit('Save changes');
form_end();

section_subhead('Delete rule');
form_start();
form_input_checkbox('Delete confirmation');
form_hidden('action', 'delete');
form_hidden('id', $_GET['id']);
form_button_submit('Delete rule', 'danger');
form_end();

foot();