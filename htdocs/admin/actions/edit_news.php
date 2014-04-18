<?php

require('../../include/mellivora.inc.php');

enforce_authentication(CONFIG_UC_MODERATOR);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    validate_id($_POST['id']);

    if ($_POST['action'] == 'edit') {

       db_update(
          'news',
          array(
             'title'=>$_POST['title'],
             'body'=>$_POST['body']
          ),
          array(
             'id'=>$_POST['id']
          )
       );

        invalidate_cache('home');

        redirect(CONFIG_SITE_ADMIN_RELPATH . 'edit_news.php?id='.$_POST['id'].'&generic_success=1');
    }

    else if ($_POST['action'] == 'delete') {

        if (!$_POST['delete_confirmation']) {
            message_error('Please confirm delete');
        }

        db_delete(
            'news',
            array(
                'id'=>$_POST['id']
            )
        );

        invalidate_cache('home');
        
        redirect(CONFIG_SITE_ADMIN_RELPATH . 'list_news.php?generic_success=1');
    }
}