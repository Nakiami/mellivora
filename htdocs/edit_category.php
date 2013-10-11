<?php

require('../include/general.inc.php');

enforce_authentication(CONFIG_UC_MODERATOR);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    validate_id($_POST['id']);

    if ($_POST['action'] == 'edit') {

       db_update(
          'categories',
          array(
             'title'=>$_POST['title'],
             'description'=>$_POST['description'],
             'available_from'=>strtotime($_POST['available_from']),
             'available_until'=>strtotime($_POST['available_until'])
          ),
          array(
             'id'=>$_POST['id']
          )
       );

        header('location: edit_category.php?id='.$_POST['id'].'&generic_success=1');
        exit();
    }

    else if ($_POST['action'] == 'delete') {

        if (!$_POST['delete_confirmation']) {
            message_error('Please confirm delete');
        }

        $stmt = $db->prepare('DELETE FROM categories WHERE id=:id');
        $stmt->execute(array(':id'=>$_POST['id']));

        // delete all the challenges and all objects related to it
        $stmt = $db->prepare('SELECT id FROM challenges WHERE category = :id');
        $stmt->execute(array(':id' => $_POST['id']));
        while ($challenge = $stmt->fetch(PDO::FETCH_ASSOC)) {
            delete_challenge_cascading($challenge['id']);
        }

        header('location: manage.php?generic_success=1');
        exit();
    }
}

validate_id($_GET['id']);

$stmt = $db->prepare('SELECT * FROM categories WHERE id = :id');
$stmt->execute(array(':id' => $_GET['id']));
$category = $stmt->fetch(PDO::FETCH_ASSOC);

head('Site management');
menu_management();

section_subhead('Edit category: ' . $category['title']);
form_start();
form_input_text('Title', $category['title']);
form_textarea('Description', $category['description']);
form_input_text('Available from', get_date_time($category['available_from']));
form_input_text('Available until', get_date_time($category['available_until']));
form_hidden('action', 'edit');
form_hidden('id', $_GET['id']);
form_button_submit('Save changes');
form_end();

section_subhead('Delete category: ' . $category['title']);
form_start();
form_input_checkbox('Delete confirmation');
form_hidden('action', 'delete');
form_hidden('id', $_GET['id']);
message_inline_warning('Warning! This will delete all challenges under this category, as well as all submissions, files, and hints related those challenges!');
form_button_submit('Delete category', 'danger');
form_end();

foot();