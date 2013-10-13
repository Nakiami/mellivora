<?php

require('../include/mellivora.inc.php');

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

        delete_cache('home');

        header('location: edit_news.php?id='.$_POST['id'].'&generic_success=1');
        exit();
    }

    else if ($_POST['action'] == 'delete') {

        if (!$_POST['delete_confirmation']) {
            message_error('Please confirm delete');
        }

        $stmt = $db->prepare('DELETE FROM news WHERE id=:id');
        $stmt->execute(array(':id'=>$_POST['id']));

        delete_cache('home');
        
        header('location: list_news.php?generic_success=1');
        exit();
    }
}

validate_id($_GET['id']);

$stmt = $db->prepare('SELECT * FROM news WHERE id = :id');
$stmt->execute(array(':id' => $_GET['id']));
$news = $stmt->fetch(PDO::FETCH_ASSOC);

head('Site management');
menu_management();

section_subhead('Edit news item: ' . $news['title']);
form_start();
form_input_text('Title', $news['title']);
form_textarea('Body', $news['body']);
form_hidden('action', 'edit');
form_hidden('id', $_GET['id']);
form_button_submit('Save changes');
form_bbcode_manual();
form_end();

section_subhead('Delete news item');
form_start();
form_input_checkbox('Delete confirmation');
form_hidden('action', 'delete');
form_hidden('id', $_GET['id']);
form_button_submit('Delete news item', 'danger');
form_end();

foot();