<?php

define('IN_FILE', true);
require('../include/general.inc.php');

enforceAuthentication(CONFIG_UC_MODERATOR);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if ($_POST['action'] == 'new') {

        $stmt = $db->prepare('
        INSERT INTO news
        (
        added,
        added_by,
        title,
        body
        )
        VALUES (
        UNIX_TIMESTAMP(),
        :user,
        :title,
        :body
        )
        ');

        $stmt->execute(array(
            ':user'=>$_SESSION['id'],
            ':title'=>$_POST['title'],
            ':body'=>$_POST['body']
        ));

        if ($db->lastInsertId()) {
            header('location: edit_news.php?id=' . $db->lastInsertId());
            exit();
        } else {
            errorMessage('Could not insert new news item:' . $stmt->errorCode());
        }
    }
}

head('Site management');
sectionSubHead('New news post');

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

    <div class="form-actions">
        <button type="submit" class="btn btn-primary">Publish news item</button>
    </div>

</form>
';

foot();