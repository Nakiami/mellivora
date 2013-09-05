<?php

define('IN_FILE', true);
require('../include/general.inc.php');

enforceAuthentication(CONFIG_UC_MODERATOR);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if ($_POST['action'] == 'edit' && isValidID($_POST['id'])) {

        $stmt = $db->prepare('
        UPDATE challenges SET
        title=:title,
        description=:description,
        flag=:flag,
        points=:points,
        category=:category,
        available_from=:available_from,
        available_until=:available_until,
        num_attempts_allowed=:num_attempts_allowed
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
            ':num_attempts_allowed'=>$_POST['num_attempts_allowed'],
            ':id'=>$_POST['id']
        ));

        header('location: edit_challenge.php?id='.$_POST['id'].'&generic_success=1');
        exit();
    }

    else if ($_POST['action'] == 'delete' && isValidID($_POST['id'])) {

        if (!$_POST['delete_confirmation']) {
            errorMessage('Please confirm delete');
        }

        $stmt = $db->prepare('DELETE FROM challenges WHERE id=:id');
        $stmt->execute(array(':id'=>$_POST['id']));

        $stmt = $db->prepare('DELETE FROM submissions WHERE challenge=:id');
        $stmt->execute(array(':id'=>$_POST['id']));

        $stmt = $db->prepare('SELECT id FROM files WHERE challenge=:id');
        $stmt->execute(array(':id'=>$_POST['id']));
        while ($file = $stmt->fetch(PDO::FETCH_ASSOC)) {

            $del_stmt = $db->prepare('DELETE FROM files WHERE id=:id');
            $del_stmt->execute(array(':id'=>$file['id']));

            unlink(CONFIG_FILE_UPLOAD_PATH . $file['id']);
        }

        header('location: manage.php?generic_success=1');
        exit();
    }

    else if ($_POST['action'] == 'upload_file' && isValidID($_POST['id'])) {

        if ($_FILES['file']['size'] > CONFIG_MAX_FILE_UPLOAD_SIZE) {
            errorMessage('File too large.');
        }

        $stmt = $db->prepare('
        INSERT INTO files
        (
        added,
        added_by,
        title,
        size,
        challenge
        )
        VALUES (
        UNIX_TIMESTAMP(),
        :user,
        :title,
        :size,
        :challenge
        )
        ');

        $stmt->execute(array(
            ':user'=>$_SESSION['id'],
            ':title'=>$_FILES['file']['name'],
            ':size'=>$_FILES['file']['size'],
            ':challenge'=>$_POST['id']
        ));

        $file_id = $db->lastInsertId();

        if (file_exists(CONFIG_FILE_UPLOAD_PATH . $file_id)) {
            errorMessage('File already existed! This should never happen!');
        }

        else {
            move_uploaded_file($_FILES['file']['tmp_name'], CONFIG_FILE_UPLOAD_PATH . $file_id);
        }

        if (!file_exists(CONFIG_FILE_UPLOAD_PATH . $file_id)) {
            errorMessage('File upload failed!');
        }

        header('location: edit_challenge.php?id='.$_POST['id'].'&generic_success=1');
        exit();
    }

    else if ($_POST['action'] == 'delete_file' && isValidID($_POST['id'])) {
        $stmt = $db->prepare('DELETE FROM files WHERE id=:id');
        $stmt->execute(array(':id'=>$_POST['id']));

        unlink(CONFIG_FILE_UPLOAD_PATH . $_POST['id']);

        header('location: edit_challenge.php?id='.$_POST['challenge_id'].'&generic_success=1');
        exit();
    }
}

if (isValidID($_GET['id'])) {

    $stmt = $db->prepare('SELECT * FROM challenges WHERE id = :id');
    $stmt->execute(array(':id' => $_GET['id']));
    $challenge = $stmt->fetch(PDO::FETCH_ASSOC);

    head('Site management');
    managementMenu();
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
        </div>

        <div class="control-group">
            <label class="control-label" for="num_attempts_allowed">Max number of flag guesses</label>
            <div class="controls">
                <input type="text" id="num_attempts_allowed" name="num_attempts_allowed" class="input-block-level" value="',htmlspecialchars($challenge['num_attempts_allowed']),'">
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


        echo '
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
    </form>
    ';

    sectionSubHead('Files');

    echo '
        <table id="files" class="table table-striped table-hover">
          <thead>
            <tr>
              <th>Filename</th>
              <th>Size</th>
              <th>Added</th>
              <th>Manage</th>
            </tr>
          </thead>
          <tbody>
        ';

    $stmt = $db->prepare('SELECT * FROM files WHERE challenge = :id');
    $stmt->execute(array(':id' => $_GET['id']));
    while ($file = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo '
            <tr>
                <td>
                    <a href="download.php?id=',htmlspecialchars($file['id']),'">',htmlspecialchars($file['title']),'</a>
                </td>
                <td>',mkSize($file['size']), '</td>
                <td>',getDateTime($file['added']),'</td>
                <td>
                    <form method="post" style="padding:0;margin:0;">
                        <input type="hidden" name="action" value="delete_file" />
                        <input type="hidden" name="id" value="',htmlspecialchars($file['id']),'" />
                        <input type="hidden" name="challenge_id" value="',htmlspecialchars($_GET['id']),'" />
                        <button type="submit" class="btn btn-small btn-danger">Delete</button>
                    </form>
                </td>
            </tr>
        ';
    }

    echo '
            </tbody>
         </table>

        <form method="post" class="form-inline" enctype="multipart/form-data">
            <input type="file" name="file" id="file" />

            <input type="hidden" name="action" value="upload_file" />
            <input type="hidden" name="id" value="',htmlspecialchars($_GET['id']),'" />

            <button type="submit" class="btn btn-small btn-primary">Upload file</button>

            Max file size: ',mkSize(min(getPHPBytes(ini_get('post_max_size')), CONFIG_MAX_FILE_UPLOAD_SIZE)),'
        </form>
        ';

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

        <div class="alert alert-error">Warning! This will also delete all submissions and all files associated with challenge!</div>

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