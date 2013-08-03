<?php

define('IN_FILE', true);
require('../include/general.inc.php');

enforceAuthentication(CONFIG_UC_MODERATOR);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if ($_POST['action'] == 'new') {

        $stmt = $db->prepare('
        INSERT INTO categories
        (
        added,
        added_by,
        title,
        description,
        available_from,
        available_until
        )
        VALUES (
        UNIX_TIMESTAMP(),
        :user,
        :title,
        :description,
        :available_from,
        :available_until
        )
        ');

        $available_from = strtotime($_POST['available_from']);
        $available_until = strtotime($_POST['available_until']);

        $stmt->execute(array(
            ':user'=>$_SESSION['id'],
            ':title'=>$_POST['title'],
            ':description'=>$_POST['description'],
            ':available_from'=>$available_from,
            ':available_until'=>$available_until
        ));

        if ($db->lastInsertId()) {
            header('location: edit_category.php?id=' . $db->lastInsertId());
            exit();
        } else {
            errorMessage('Could not insert new category:' . $stmt->errorCode());
        }
    }
}

head('Site management');
managementMenu();
sectionSubHead('New category');

echo '
<form class="form-horizontal" method="post">

    <div class="control-group">
        <label class="control-label" for="title">Title</label>
        <div class="controls">
            <input type="text" id="title" name="title" class="input-block-level" placeholder="Title">
        </div>
    </div>

    <div class="control-group">
        <label class="control-label" for="description">Description</label>
        <div class="controls">
            <textarea id="description" name="description" class="input-block-level" rows="10"></textarea>
        </div>
    </div>

    <div class="control-group">
        <label class="control-label" for="available_from">Available from</label>
        <div class="controls">
            <input type="text" id="available_from" name="available_from" class="input-block-level" value="',getDateTime(),'">
        </div>
    </div>

    <div class="control-group">
        <label class="control-label" for="available_until">Available until</label>
        <div class="controls">
            <input type="text" id="available_until" name="available_until" class="input-block-level" value="',getDateTime(),'">
        </div>
    </div>

    <input type="hidden" name="action" value="new" />

    <div class="control-group">
        <label class="control-label" for="save"></label>
        <div class="controls">
            <button type="submit" id="save" class="btn btn-primary">Create category</button>
        </div>
    </div>

</form>
';

foot();