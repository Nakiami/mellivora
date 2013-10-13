<?php

require('../include/mellivora.inc.php');

enforce_authentication(CONFIG_UC_MODERATOR);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

   validate_id($_POST['id']);

    if ($_POST['action'] == 'edit') {

        db_update(
           'hints',
           array(
              'body'=>$_POST['body'],
              'challenge'=>$_POST['challenge'],
              'visible'=>($_POST['visible'] ? 1 : 0)
           ),
           array(
              'id'=>$_POST['id']
           )
        );

        delete_cache('hints');

        header('location: edit_hint.php?id='.htmlspecialchars($_POST['id']).'&generic_success=1');
        exit();
    }

    else if ($_POST['action'] == 'delete') {

        if (!$_POST['delete_confirmation']) {
            message_error('Please confirm delete');
        }

        $stmt = $db->prepare('DELETE FROM hints WHERE id=:id');
        $stmt->execute(array(':id'=>$_POST['id']));

        delete_cache('hints');

        header('location: list_hints.php?generic_success=1');
        exit();
    }
}

validate_id($_GET['id']);

head('Site management');
menu_management();
section_subhead('Edit hint');

$stmt = $db->prepare('SELECT * FROM hints WHERE id=:id');
$stmt->execute(array(':id' => $_GET['id']));
$hint = $stmt->fetch(PDO::FETCH_ASSOC);

form_start();
form_textarea('Body', $hint['body']);
$stmt = $db->query('SELECT
                    ch.id,
                    ch.title,
                    ca.title AS category
                  FROM challenges AS ch
                  LEFT JOIN categories AS ca ON ca.id = ch.category
                  ORDER BY ca.title, ch.title
                  ');
form_select($stmt, 'Challenge', 'id', $hint['challenge'], 'title', 'category');
form_input_checkbox('Visible', $hint['visible']);
form_hidden('action', 'edit');
form_hidden('id', $_GET['id']);
form_button_submit('Save changes');
form_end();

section_subhead('Delete hint');
form_start();
form_input_checkbox('Delete confirmation');
form_hidden('action', 'delete');
form_hidden('id', $_GET['id']);
form_button_submit('Delete hint', 'danger');
form_end();

foot();