<?php

require('../include/mellivora.inc.php');

enforce_authentication(CONFIG_UC_MODERATOR);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if ($_POST['action'] == 'new') {

        $id = db_insert(
          'hints',
          array(
             'added'=>time(),
             'added_by'=>$_SESSION['id'],
             'challenge'=>$_POST['challenge'],
             'visible'=>$_POST['visible'],
             'body'=>$_POST['body']
          )
        );

        if ($id) {
            invalidate_cache('hints');

            header('location: edit_hint.php?id='.$id);
            exit();
        } else {
            message_error('Could not insert new hint: '.$db->errorCode());
        }
    }
}

head('Site management');
menu_management();

section_subhead('New hint');
form_start();
form_textarea('Body');
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
form_hidden('action', 'new');
form_button_submit('Create hint');
form_end();

foot();