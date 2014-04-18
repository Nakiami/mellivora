<?php

require('../../include/mellivora.inc.php');

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

        // delete all the challenges and all objects related to it
        $stmt = $db->prepare('SELECT id FROM challenges WHERE category = :id');
        $stmt->execute(array(':id' => $_POST['id']));
        while ($challenge = $stmt->fetch(PDO::FETCH_ASSOC)) {
            delete_challenge_cascading($challenge['id']);
        }

        redirect(CONFIG_SITE_ADMIN_RELPATH . 'manage.php?generic_success=1');
    }
}