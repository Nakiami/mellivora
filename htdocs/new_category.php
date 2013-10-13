<?php

require('../include/mellivora.inc.php');

enforce_authentication(CONFIG_UC_MODERATOR);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if ($_POST['action'] == 'new') {

       $id = db_insert(
          'categories',
          array(
             'added'=>time(),
             'added_by'=>$_SESSION['id'],
             'title'=>$_POST['title'],
             'description'=>$_POST['description'],
             'available_from'=>strtotime($_POST['available_from']),
             'available_until'=>strtotime($_POST['available_until'])
          )
       );

        if ($id) {
            header('location: edit_category.php?id='.$id);
            exit();
        } else {
            message_error('Could not insert new category: '.$db->errorCode());
        }
    }
}

head('Site management');
menu_management();

section_subhead('New category');
form_start();
form_input_text('Title');
form_textarea('Description');
form_input_text('Available from', date_time());
form_input_text('Available until', date_time());
form_hidden('action', 'new');
form_button_submit('Create category');
form_end();

foot();