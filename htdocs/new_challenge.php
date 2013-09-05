<?php

define('IN_FILE', true);
require('../include/general.inc.php');

enforceAuthentication(CONFIG_UC_MODERATOR);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if ($_POST['action'] == 'new') {

        $stmt = $db->prepare('
        INSERT INTO challenges (
        added,
        added_by,
        title,
        description,
        flag,
        points,
        category,
        available_from,
        available_until,
        num_attempts_allowed
        ) VALUES (
        UNIX_TIMESTAMP(),
        :user,
        :title,
        :description,
        :flag,
        :points,
        :category,
        :available_from,
        :available_until,
        :num_attempts_allowed
        )
        ');

        $available_from = strtotime($_POST['available_from']);
        $available_until = strtotime($_POST['available_until']);

        $stmt->execute(array(
            ':user'=>$_SESSION['id'],
            ':title'=>$_POST['title'],
            ':description'=>$_POST['description'],
            ':flag'=>$_POST['flag'],
            ':points'=>$_POST['points'],
            ':category'=>$_POST['category'],
            ':available_from'=>$available_from,
            ':available_until'=>$available_until,
            ':num_attempts_allowed'=>$_POST['num_attempts_allowed']
        ));

        if ($db->lastInsertId()) {
            header('location: edit_challenge.php?id=' . $db->lastInsertId(). '&generic_success=1');
            exit();
        } else {
            errorMessage('Could not insert new challenge: ' . $stmt->errorCode());
        }
    }
}

head('Site management');
managementMenu();
sectionSubHead('New challenge');

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
        <label class="control-label" for="flag">Flag</label>
        <div class="controls">
            <input type="text" id="flag" name="flag" class="input-block-level" placeholder="Flag">
        </div>
    </div>

    <div class="control-group">
        <label class="control-label" for="points">Points</label>
        <div class="controls">
            <input type="text" id="points" name="points" class="input-block-level" placeholder="Points">
        </div>
    </div>

    <div class="control-group">
        <label class="control-label" for="num_attempts_allowed">Max number of flag guesses</label>
        <div class="controls">
            <input type="text" id="num_attempts_allowed" name="num_attempts_allowed" class="input-block-level" value="5">
        </div>
    </div>';


    echo '
    <div class="control-group">
        <label class="control-label" for="category">Category</label>
        <div class="controls">

        <select id="category" name="category">';
    $stmt = $db->query('SELECT * FROM categories ORDER BY title');
    while ($category = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo '<option value="',htmlspecialchars($category['id']),'"',($category['id'] == $_GET['category'] ? ' selected="selected"' : ''),'>', htmlspecialchars($category['title']), '</option>';
    }
    echo '
        </select>

        </div>
    </div>
    ';


    echo '<div class="control-group">
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
    </div>';

    echo '
        <div class="control-group">
            <label class="control-label" for="files">Files</label>
            <div class="controls">
                <input type="text" id="files" class="input-block-level" value="Create and edit challenge to add files." disabled />
         </div>
     </div>

    <input type="hidden" name="action" value="new" />

    <div class="control-group">
        <label class="control-label" for="save"></label>
        <div class="controls">
            <button type="submit" id="save" class="btn btn-primary">Create challenge</button>
        </div>
    </div>

</form>
';

foot();