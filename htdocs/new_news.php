<?php

require('../include/general.inc.php');

enforce_authentication(CONFIG_UC_MODERATOR);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if ($_POST['action'] == 'new') {

       $id = db_insert(
          'hints',
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

echo '
<form class="form-horizontal" method="post">

    <div class="control-group">
        <label class="control-label" for="title">Title</label>
        <div class="controls">
            <input type="text" id="title" name="title" class="input-block-level" placeholder="Title">
        </div>
    </div>

    <div class="control-group">
        <label class="control-label" for="body">Body</label>
        <div class="controls">
            <textarea id="body" name="body" class="input-block-level" rows="10"></textarea>
        </div>
    </div>

    <input type="hidden" name="action" value="new" />

    <div class="control-group">
        <label class="control-label" for="save"></label>
        <div class="controls">
            <button type="submit" id="save" class="btn btn-primary">Publish news item</button>
        </div>
    </div>

    <div class="control-group">
        <label class="control-label" for="bbcode">BBcode</label>
        <div class="controls">
            ',bbcode_manual(),'
        </div>
    </div>

</form>
';

foot();