<?php

require('../include/general.inc.php');

enforce_authentication(CONFIG_UC_MODERATOR);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    validate_id($_POST['id']);

    if ($_POST['action'] == 'edit') {

       db_update(
            'challenges',
            array(
                'title'=>$_POST['title'],
                'description'=>$_POST['description'],
                'flag'=>$_POST['flag'],
                'case_insensitive'=>$_POST['case_insensitive'],
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
            message_error('Please confirm delete');
        }

        delete_challenge_cascading($_POST['id']);

        header('location: manage.php?generic_success=1');
        exit();
    }

    else if ($_POST['action'] == 'upload_file') {

        if ($_FILES['file']['size'] > CONFIG_MAX_FILE_UPLOAD_SIZE) {
            message_error('File too large.');
        }

        $file_id = db_insert(
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
            message_error('File already existed! This should never happen!');
        }

        else {
            move_uploaded_file($_FILES['file']['tmp_name'], CONFIG_FILE_UPLOAD_PATH . $file_id);
        }

        if (!file_exists(CONFIG_FILE_UPLOAD_PATH . $file_id)) {
            message_error('File upload failed!');
        }

        header('location: edit_challenge.php?id='.$_POST['id'].'&generic_success=1');
        exit();
    }

    else if ($_POST['action'] == 'delete_file') {
        delete_file($_POST['id']);

        header('location: edit_challenge.php?id='.$_POST['challenge_id'].'&generic_success=1');
        exit();
    }
}

validate_id($_GET['id']);

$stmt = $db->prepare('SELECT * FROM challenges WHERE id=:id');
$stmt->execute(array(':id' => $_GET['id']));
$challenge = $stmt->fetch(PDO::FETCH_ASSOC);

head('Site management');
menu_management();

section_subhead('Edit challenge: ' . $challenge['title']);
form_start();
form_input_text('Title', $challenge['title']);
form_textarea('Description', $challenge['description']);

form_input_text('Flag', $challenge['flag']);
form_input_checkbox('Case insensitive', $challenge['case_insensitive']);

form_input_text('Points', $challenge['points']);
form_input_text('Num attempts allowed', $challenge['num_attempts_allowed']);

$stmt = $db->query('SELECT * FROM categories ORDER BY title');
form_select($stmt, 'Category', 'id', $challenge['category'], 'title');

form_input_text('Available from', get_date_time($challenge['available_from']));
form_input_text('Available until', get_date_time($challenge['available_until']));

form_hidden('action', 'edit');
form_hidden('id', $_GET['id']);

form_button_submit('Save changes');
form_end();

section_subhead('Files');
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
          <td>',mk_size($file['size']), '</td>
          <td>',get_date_time($file['added']),'</td>
          <td>';
            form_start('', 'no_padding_or_margin');
            form_hidden('action', 'delete_file');
            form_hidden('id', $file['id']);
            form_hidden('challenge_id', $_GET['id']);
            form_button_submit('Delete');
            form_end();
          echo '
          </td>
      </tr>
  ';
}

echo '
      </tbody>
   </table>
';

form_start('multipart/form-data');
form_file('file');
form_hidden('action', 'upload_file');
form_hidden('id', $_GET['id']);
form_button_submit('Upload file');
echo 'Max file size: ',mk_size(max_file_upload_size());
form_end();

section_subhead('Hints');
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
      <td>',get_date_time($hint['added']),'</td>
      <td>',htmlspecialchars($hint['body']),'</td>
      <td><a href="edit_hint.php?id=',htmlspecialchars(short_description($hint['id'], 100)),'" class="btn btn-mini btn-primary">Edit</a></td>
  </tr>
  ';
}
echo '
</tbody>
</table>

<a href="new_hint.php?id=',htmlspecialchars($_GET['id']),'" class="btn btn-small btn-warning">Add a new hint</a>
';

section_subhead('Delete challenge: ' . $challenge['title']);
form_start();
form_input_checkbox('Delete confirmation');
form_hidden('action', 'delete');
form_hidden('id', $_GET['id']);
message_inline_warning('Warning! This will also delete all submissions, all hints and all files associated with challenge!');
form_button_submit('Delete challenge', 'danger');
form_end();

foot();