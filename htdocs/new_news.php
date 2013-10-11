<?php

require('../include/general.inc.php');

enforce_authentication(CONFIG_UC_MODERATOR);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if ($_POST['action'] == 'new') {

       $id = db_insert(
          'news',
          array(
             'added'=>time(),
             'added_by'=>$_SESSION['id'],
             'title'=>$_POST['title'],
             'body'=>$_POST['body']
          )
       );

       if ($id) {
          delete_cache('home');

          header('location: edit_news.php?id='.$id);
          exit();
       } else {
          message_error('Could not insert new news item: '.$db->errorCode());
       }
    }
}

head('Site management');
menu_management();

section_subhead('New news post');
form_start();
form_input_text('Title');
form_textarea('Body');
form_hidden('action', 'new');
form_button_submit('Publish news item');
form_bbcode_manual();
form_end();

foot();