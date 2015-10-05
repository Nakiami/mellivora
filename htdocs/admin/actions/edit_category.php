<?php

require('../../../include/mellivora.inc.php');

enforce_authentication(CONST_USER_CLASS_MODERATOR);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    validate_id($_POST['id']);
    validate_xsrf_token($_POST[CONST_XSRF_TOKEN_KEY]);

    if ($_POST['action'] == 'edit') {

       db_update(
          'categories',
          array(
             'title'=>$_POST['title'],
             'description'=>$_POST['description'],
             'exposed'=>$_POST['exposed'],
             'available_from'=>strtotime($_POST['available_from']),
             'available_until'=>strtotime($_POST['available_until'])
          ),
          array(
             'id'=>$_POST['id']
          )
       );

        redirect(CONFIG_SITE_ADMIN_RELPATH . 'edit_category.php?id='.$_POST['id'].'&generic_success=1');
    }

    else if ($_POST['action'] == 'delete') {

        if (!$_POST['delete_confirmation']) {
            message_error('Please confirm delete');
        }

        db_delete(
            'categories',
            array(
                'id'=>$_POST['id']
            )
        );

        $challenges = db_select_all(
            'challenges',
            array('id'),
            array('category' => $_POST['id'])
        );

        foreach ($challenges as $challenge) {
            delete_challenge_cascading($challenge['id']);
        }

        redirect(CONFIG_SITE_ADMIN_RELPATH . '?generic_success=1');
    }
}