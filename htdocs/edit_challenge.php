<?php

define('IN_FILE', true);
require('../include/general.inc.php');

enforceAuthentication(CONFIG_UC_MODERATOR);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    validateID($_POST['id']);

    if ($_POST['action'] == 'edit') {

       dbUpdate(
            'challenges',
            array(
                'title'=>$_POST['title'],
                'description'=>$_POST['description'],
                'flag'=>$_POST['flag'],
                'points'=>$_POST['points'],
                'category'=>$_POST['category'],
                'available_from'=>strtotime($_POST['available_from']),
                'available_until'=>strtotime($_POST['available_until']),
                'num_attempts_allowed'=>$_POST['num_attempts_allowed']
            ),
            array('id'=>$_POST['id'])
        );

        header('location: edit_challenge.php?id='.$_POST['id'].'&generic_success=1');
        exit();
    }

    else if ($_POST['action'] == 'delete') {

        if (!$_POST['delete_confirmation']) {
            errorMessage('Please confirm delete');
        }

        deleteChallengeCascading($_POST['id']);

        header('location: manage.php?generic_success=1');
        exit();
    }

    else if ($_POST['action'] == 'upload_file') {

        if ($_FILES['file']['size'] > CONFIG_MAX_FILE_UPLOAD_SIZE) {
            errorMessage('File too large.');
        }

        $file_id = dbInsert(
            'files',
            array(
                'added'=>time(),
                'added_by'=>$_SESSION['id'],
                'title'=>$_FILES['file']['name'],
                'size'=>$_FILES['file']['size'],
                'challenge'=>$_POST['id']
            )
        );

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

    else if ($_POST['action'] == 'delete_file') {
        deleteFile($_POST['id']);

        header('location: edit_challenge.php?id='.$_POST['challenge_id'].'&generic_success=1');
        exit();
    }
}

validateID($_GET['id']);

$stmt = $db->prepare('SELECT * FROM challenges WHERE id=:id');
$stmt->execute(array(':id' => $_GET['id']));
$challenge = $stmt->fetch(PDO::FETCH_ASSOC);

head('Site management');
managementMenu();
sectionSubHead('Edit challenge: ' . $challenge['title']);

form_start();
form_input_text('Title', $challenge['title']);
form_textarea('Description', $challenge['description']);
form_input_text('Flag', $challenge['flag']);
form_input_text('Points', $challenge['points']);
form_input_text('Num attempts allowed', $challenge['num_attempts_allowed']);

$stmt = $db->query('SELECT * FROM categories ORDER BY title');
form_select($stmt, 'Category', 'id', $challenge['category'], 'title');

form_input_text('Available from', getDateTime($challenge['available_from']));
form_input_text('Available until', getDateTime($challenge['available_until']));

form_hidden('action', 'edit');
form_hidden('id', $_GET['id']);

form_button_submit('Save changes');
form_end();

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

sectionSubHead('Hints');
echo '
<table id="hints" class="table table-striped table-hover">
<thead>
  <tr>
    <th>Added</th>
    <th>Hint</th>
    <th>Manage</th>
  </tr>
</thead>
<tbody>
';

$stmt = $db->prepare('
  SELECT
  h.id,
  h.added,
  h.body
  FROM hints AS h
  WHERE h.challenge=:challenge
');
$stmt->execute(array(':challenge' => $_GET['id']));
while($hint = $stmt->fetch(PDO::FETCH_ASSOC)) {
  echo '
  <tr>
      <td>',getDateTime($hint['added']),'</td>
      <td>',htmlspecialchars($hint['body']),'</td>
      <td><a href="edit_hint.php?id=',htmlspecialchars(shortDescription($hint['id'], 100)),'" class="btn btn-mini btn-primary">Edit</a></td>
  </tr>
  ';
}
echo '
</tbody>
</table>

<a href="new_hint.php?id=',htmlspecialchars($_GET['id']),'" class="btn btn-small btn-warning">Add a new hint</a>
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

  <div class="alert alert-error">Warning! This will also delete all submissions, all hints and all files associated with challenge!</div>

  <div class="control-group">
      <label class="control-label" for="delete"></label>
      <div class="controls">
          <button type="submit" id="delete" class="btn btn-danger">Delete challenge</button>
      </div>
  </div>
</form>
';

foot();