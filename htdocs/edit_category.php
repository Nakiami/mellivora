<?php

define('IN_FILE', true);
require('../include/general.inc.php');

enforceAuthentication(CONFIG_UC_MODERATOR);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if ($_POST['action'] == 'edit' && is_valid_id($_POST['id'])) {

        $stmt = $db->prepare('
        UPDATE categories SET
        title=:title,
        description=:description,
        available_from=:available_from,
        available_until=:available_until
        WHERE id=:id
        ');

        $available_from = strtotime($_POST['available_from']);
        $available_until = strtotime($_POST['available_until']);

        $stmt->execute(array(
            ':title'=>$_POST['title'],
            ':description'=>$_POST['description'],
            ':available_from'=>$available_from,
            ':available_until'=>$available_until,
            ':id'=>$_POST['id']
        ));

        header('location: edit_category.php?id='.$_POST['id'].'&generic_success=1');
        exit();
    }

    else if ($_POST['action'] == 'delete' && is_valid_id($_POST['id'])) {

        if (!$_POST['delete_confirmation']) {
            errorMessage('Please confirm delete');
        }

        $stmt = $db->prepare('DELETE FROM categories WHERE id=:id');
        $stmt->execute(array(':id'=>$_POST['id']));

        $stmt = $db->prepare('SELECT id FROM challenges WHERE category = :id');
        $stmt->execute(array(':id' => $_POST['id']));
        while ($challenge = $stmt->fetch(PDO::FETCH_ASSOC)) {

            $c_stmt = $db->prepare('DELETE FROM challenges WHERE id=:id');
            $c_stmt->execute(array(':id'=>$challenge['id']));

            $s_stmt = $db->prepare('DELETE FROM submissions WHERE challenge=:id');
            $s_stmt->execute(array(':id'=>$challenge['id']));
        }

        header('location: manage.php?generic_success=1');
        exit();
    }
}

if (is_valid_id($_GET['id'])) {

    $stmt = $db->prepare('SELECT * FROM categories WHERE id = :id');
    $stmt->execute(array(':id' => $_GET['id']));
    $challenge = $stmt->fetch(PDO::FETCH_ASSOC);

    head('Site management');
    sectionSubHead('Edit challenge: ' . $challenge['title']);

    echo '
    <form class="form-horizontal" method="post">

        <div class="control-group">
            <label class="control-label" for="title">Title</label>
            <div class="controls">
                <input type="text" id="title" name="title" class="input-block-level" placeholder="Title" value="',htmlspecialchars($challenge['title']),'">
            </div>
        </div>

        <div class="control-group">
            <label class="control-label" for="description">Description</label>
            <div class="controls">
                <textarea id="description" name="description" class="input-block-level" rows="10">',htmlspecialchars($challenge['description']),'</textarea>
            </div>
        </div>

        <div class="control-group">
            <label class="control-label" for="available_from">Available from</label>
            <div class="controls">
                <input type="text" id="available_from" name="available_from" class="input-block-level" placeholder="Available from" value="',getDateTime($challenge['available_from']),'">
            </div>
        </div>

        <div class="control-group">
            <label class="control-label" for="available_until">Available until</label>
            <div class="controls">
                <input type="text" id="available_until" name="available_until" class="input-block-level" placeholder="Available until" value="',getDateTime($challenge['available_until']),'">
            </div>
        </div>

        <input type="hidden" name="action" value="edit" />
        <input type="hidden" name="id" value="',htmlspecialchars($_GET['id']),'" />

        <div class="control-group">
            <label class="control-label" for="save"></label>
            <div class="controls">
                <button type="submit" id="save" class="btn btn-primary">Save changes</button>
            </div>
        </div>

    </form>';

    sectionSubHead('Delete challenge: ' . $challenge['title']);

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

        <div class="alert alert-error">Warning! This will delete all challenges under this category, as well as all submissions to those challenges!</div>

        <div class="control-group">
            <label class="control-label" for="delete"></label>
            <div class="controls">
                <button type="submit" id="delete" class="btn btn-danger">Delete challenge</button>
            </div>
        </div>
    </form>
    ';
}

foot();