<?php

require('../include/general.inc.php');

enforce_authentication(CONFIG_UC_MODERATOR);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

   validate_id($_POST['id']);

    if ($_POST['action'] == 'edit') {

        db_update(
           'hints',
           array(
              'body'=>$_POST['body'],
              'challenge'=>$_POST['challenge'],
              'visible'=>($_POST['visible'] ? 1 : 0)
           ),
           array(
              'id'=>$_POST['id']
           )
        );

        delete_cache('hints');

        header('location: edit_hint.php?id='.htmlspecialchars($_POST['id']).'&generic_success=1');
        exit();
    }

    else if ($_POST['action'] == 'delete') {

        if (!$_POST['delete_confirmation']) {
            message_error('Please confirm delete');
        }

        $stmt = $db->prepare('DELETE FROM hints WHERE id=:id');
        $stmt->execute(array(':id'=>$_POST['id']));

        delete_cache('hints');

        header('location: list_hints.php?generic_success=1');
        exit();
    }
}

validate_id($_GET['id']);

head('Site management');
menu_management();
section_subhead('Edit hint');

$stmt = $db->prepare('SELECT * FROM hints WHERE id=:id');
$stmt->execute(array(':id' => $_GET['id']));
$hint = $stmt->fetch(PDO::FETCH_ASSOC);

echo '
<form class="form-horizontal" method="post">

  <div class="control-group">
      <label class="control-label" for="description">Body</label>
      <div class="controls">
          <textarea id="body" name="body" class="input-block-level" rows="10">',htmlspecialchars($hint['body']),'</textarea>
      </div>
  </div>
  ';

echo '
  <div class="control-group">
      <label class="control-label" for="challenge">Challenge</label>
      <div class="controls">

      <select id="challenge" name="challenge">';
$stmt = $db->query('SELECT
                    ch.id,
                    ch.title,
                    ca.title AS category
                  FROM challenges AS ch
                  LEFT JOIN categories AS ca ON ca.id = ch.category
                  ORDER BY ca.title, ch.title
                  ');
$category = '';
while ($challenge = $stmt->fetch(PDO::FETCH_ASSOC)) {
  if ($category != $challenge['category']) {
      if ($category) {
          echo '</optgroup>';
      }
      echo '<optgroup label="',htmlspecialchars($challenge['category']),'">';
  }

  echo '<option value="',htmlspecialchars($challenge['id']),'"',($challenge['id'] == $hint['challenge'] ? ' selected="selected"' : ''),'>', htmlspecialchars($challenge['title']), '</option>';

  $category = $challenge['category'];
}
echo '
      </optgroup>
      </select>

      </div>
  </div>
  ';

echo '

  <div class="control-group">
      <label class="control-label" for="visible">Visible</label>
      <div class="controls">
          <input type="checkbox" id="visible" name="visible" value="1"',($hint['visible'] ? ' checked="checked"' : ''),' />
      </div>
  </div>

  <input type="hidden" name="action" value="edit" />
  <input type="hidden" name="id" value="',htmlspecialchars($_GET['id']),'" />

  <div class="control-group">
      <label class="control-label" for="save"></label>
      <div class="controls">
          <button type="submit" id="save" class="btn btn-primary">Edit hint</button>
      </div>
  </div>

</form>
';

section_subhead('Delete hint');
echo '
<form class="form-horizontal"  method="post">
  <div class="control-group">
      <label class="control-label" for="delete_confirmation">I want to delete this hint.</label>

      <div class="controls">
          <input type="checkbox" id="delete_confirmation" name="delete_confirmation" value="1" />
      </div>
  </div>

  <input type="hidden" name="action" value="delete" />
  <input type="hidden" name="id" value="',htmlspecialchars($_GET['id']),'" />

  <div class="control-group">
      <label class="control-label" for="delete"></label>
      <div class="controls">
          <button type="submit" id="delete" class="btn btn-danger">Delete hint</button>
      </div>
  </div>
</form>
';

foot();