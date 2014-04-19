<?php

require('../../../include/mellivora.inc.php');

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
          invalidate_cache('home');

          redirect(CONFIG_SITE_ADMIN_RELPATH . 'edit_news.php?id='.$id);
       } else {
          message_error('Could not insert new news item.');
       }
    }
}