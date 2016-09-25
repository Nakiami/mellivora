<?php

require('../../../include/mellivora.inc.php');

enforce_authentication(CONST_USER_CLASS_MODERATOR);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    validate_xsrf_token($_POST[CONST_XSRF_TOKEN_KEY]);

    if ($_POST['action'] == 'new') {

       require_fields(array('title'), $_POST);

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
          invalidate_cache(CONST_CACHE_NAME_HOME);
          redirect(CONFIG_SITE_ADMIN_RELPATH . 'edit_news.php?id='.$id);
       } else {
          message_error('Could not insert new news item.');
       }
    }
}