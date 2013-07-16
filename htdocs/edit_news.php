<?php

define('IN_FILE', true);
require('../include/general.inc.php');

enforceAuthentication(CONFIG_UC_MODERATOR);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if ($_POST['action'] == 'edit' && is_valid_id($_POST['id'])) {

        $stmt = $db->prepare('
        UPDATE news SET
        title=:title,
        body=:body
        WHERE id=:id
        ');

        $stmt->execute(array(
            ':title'=>$_POST['title'],
            ':body'=>$_POST['body'],
            ':id'=>$_POST['id']
        ));
    }

    else if ($_POST['action'] == 'delete' && is_valid_id($_POST['id'])) {

        if (!$_POST['delete_confirmation']) {
            errorMessage('Please confirm delete');
        }

        $stmt = $db->prepare('DELETE FROM news WHERE id=:id');
        $stmt->execute(array(':id'=>$_POST['id']));
        
        header('location: list_news.php');
        exit();
    }
}

if (is_valid_id($_GET['id'])) {

    $stmt = $db->prepare('SELECT * FROM news WHERE id = :id');
    $stmt->execute(array(':id' => $_GET['id']));
    $news = $stmt->fetch(PDO::FETCH_ASSOC);

    head('Site management');
    sectionSubHead('Edit news item: ' . $news['title']);

    echo '
    <form class="form-horizontal" method="post">

        <div class="control-group">
            <label class="control-label" for="title">Title</label>
            <div class="controls">
                <input type="text" id="title" name="title" class="input-block-level" placeholder="Title" value="',htmlspecialchars($news['title']),'">
            </div>
        </div>

        <div class="control-group">
            <label class="control-label" for="body">Body</label>
            <div class="controls">
                <textarea id="body" name="body" class="input-block-level" rows="10">',htmlspecialchars($news['body']),'</textarea>
            </div>
        </div>

        <input type="hidden" name="action" value="edit" />
        <input type="hidden" name="id" value="',htmlspecialchars($_GET['id']),'" />

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Save changes</button>
        </div>

    </form>';

    sectionSubHead('Delete news item: ' . $news['title']);

    echo '
    <form class="form-horizontal"  method="post">
        <div class="control-group">
            <label class="control-label" for="delete_confirmation">I want to delete this category.</label>

            <div class="controls">
                <input type="checkbox" id="delete_confirmation" name="delete_confirmation" value="1" />
            </div>
        </div>

        <input type="hidden" name="action" value="delete" />
        <input type="hidden" name="id" value="',htmlspecialchars($_GET['id']),'" />

        <div class="form-actions">
            <button type="submit" class="btn btn-danger">Delete news item</button>
        </div>
    </form>
    ';
}

foot();