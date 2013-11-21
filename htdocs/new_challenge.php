<?php

require('../include/mellivora.inc.php');

enforce_authentication(CONFIG_UC_MODERATOR);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if ($_POST['action'] == 'new') {

       $id = db_insert(
          'challenges',
          array(
             'added'=>time(),
             'added_by'=>$_SESSION['id'],
             'title'=>$_POST['title'],
             'description'=>$_POST['description'],
             'flag'=>$_POST['flag'],
             'automark'=>$_POST['automark'],
             'case_insensitive'=>$_POST['case_insensitive'],
             'points'=>$_POST['points'],
             'category'=>$_POST['category'],
             'num_attempts_allowed'=>$_POST['num_attempts_allowed'],
             'available_from'=>strtotime($_POST['available_from']),
             'available_until'=>strtotime($_POST['available_until'])
          )
       );

       if ($id) {
          header('location: edit_challenge.php?id='.$id);
          exit();
       } else {
          message_error('Could not insert new challenge: '.$db->errorCode());
       }
    }
}

head('Site management');
menu_management();
section_subhead('New challenge');

form_start();
form_input_text('Title');
form_textarea('Description');

form_textarea('Flag');
form_input_checkbox('Automark', true);
form_input_checkbox('Case insensitive');

form_input_text('Points');
form_input_text('Num attempts allowed');

$stmt = $db->query('SELECT * FROM categories ORDER BY title');
form_select($stmt, 'Category', 'id', '', 'title');

form_input_text('Available from', date_time());
form_input_text('Available until', date_time());

message_inline_info('Create and edit challenge to add files.');

form_hidden('action', 'new');

form_button_submit('Create challenge');
form_end();

foot();