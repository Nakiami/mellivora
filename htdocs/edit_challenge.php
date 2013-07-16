<?php

define('IN_FILE', true);
require('../include/general.inc.php');

enforceAuthentication(CONFIG_UC_MODERATOR);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if ($_POST['action'] == 'edit' && is_valid_id($_POST['id'])) {

        $stmt = $db->prepare('
        UPDATE challenges SET
        title=:title,
        description=:description,
        flag=:flag,
        points=:points,
        category=:category,
        available_from=:available_from,
        available_until=:available_until
        WHERE id=:id
        ');

        $available_from = strtotime($_POST['available_from']);
        $available_until = strtotime($_POST['available_until']);

        $stmt->execute(array(
            ':title'=>$_POST['title'],
            ':description'=>$_POST['description'],
            ':flag'=>$_POST['flag'],
            ':points'=>$_POST['points'],
            ':category'=>$_POST['category'],
            ':available_from'=>$available_from,
            ':available_until'=>$available_until,
            ':id'=>$_POST['id']
        ));

        header('location: edit_challenge.php?id='.$_POST['id'].'&generic_success=1');
        exit();
    }

    else if ($_POST['action'] == 'delete' && is_valid_id($_POST['id'])) {

        if (!$_POST['delete_confirmation']) {
            errorMessage('Please confirm delete');
        }

        $stmt = $db->prepare('DELETE FROM challenges WHERE id=:id');
        $stmt->execute(array(':id'=>$_POST['id']));

        $stmt = $db->prepare('DELETE FROM submissions WHERE challenge=:id');
        $stmt->execute(array(':id'=>$_POST['id']));

        header('location: manage.php?generic_success=1');
        exit();
    }
}

if (is_valid_id($_GET['id'])) {

    $stmt = $db->prepare('SELECT * FROM challenges WHERE id = :id');
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
            <label class="control-label" for="flag">Flag</label>
            <div class="controls">
                <input type="text" id="flag" name="flag" class="input-block-level" placeholder="Flag" value="',htmlspecialchars($challenge['flag']),'">
            </div>
        </div>

        <div class="control-group">
            <label class="control-label" for="points">Points</label>
            <div class="controls">
                <input type="text" id="points" name="points" class="input-block-level" placeholder="Points" value="',htmlspecialchars($challenge['points']),'">
            </div>
        </div>';


        echo '
        <div class="control-group">
            <label class="control-label" for="category">Category</label>
            <div class="controls">

            <select id="category" name="category">';
        $stmt = $db->query('SELECT * FROM categories ORDER BY title');
        while ($category = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo '<option value="',htmlspecialchars($category['id']),'"',($category['id'] == $challenge['category'] ? ' selected="selected"' : ''),'>', htmlspecialchars($category['title']), '</option>';
        }
        echo '
            </select>

            </div>
        </div>
        ';


        echo '<div class="control-group">
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
        </div>';

        echo '
            <div class="control-group">
                <label class="control-label" for="files">Files</label>
                <div class="controls">
                    <table id="files" class="table table-striped table-hover">
                      <thead>
                        <tr>
                          <th>Filename</th>
                          <th>Size</th>
                          <th>Description</th>
                          <th>Manage</th>
                        </tr>
                      </thead>
                      <tbody>
        ';
        // TODO show files here
        echo '
                </tbody>
             </table>
             </div>
         </div>

        <input type="hidden" name="action" value="edit" />
        <input type="hidden" name="id" value="',htmlspecialchars($_GET['id']),'" />

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Save changes</button>
        </div>

    </form>';

    sectionSubHead('Delete challenge: ' . $challenge['title']);

    echo '
    <form class="form-horizontal"  method="post">
        <div class="control-group">
            <label class="control-label" for="delete_confirmation">I want to delete this challenge</label>
            <div class="controls">
                <input type="checkbox" id="delete_confirmation" name="delete_confirmation" value="1" />
            </div>
        </div>

        <input type="hidden" name="action" value="delete" />
        <input type="hidden" name="id" value="',htmlspecialchars($_GET['id']),'" />

        <div class="alert alert-error">Warning! This will delete all submissions to this challenge!</div>

        <div class="form-actions">
            <button type="submit" class="btn btn-danger">Delete challenge</button>
        </div>
    </form>
    ';
}

foot();