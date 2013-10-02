<?php

define('IN_FILE', true);
require('../include/general.inc.php');

enforce_authentication(CONFIG_UC_MODERATOR);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    validate_id($_POST['id']);

    if ($_POST['action'] == 'edit') {

       db_update(
          'categories',
          array(
             'title'=>$_POST['title'],
             'description'=>$_POST['description'],
             'available_from'=>strtotime($_POST['available_from']),
             'available_until'=>strtotime($_POST['available_until'])
          ),
          array(
             'id'=>$_POST['id']
          )
       );

        header('location: edit_category.php?id='.$_POST['id'].'&generic_success=1');
        exit();
    }

    else if ($_POST['action'] == 'delete') {

        if (!$_POST['delete_confirmation']) {
            message_error('Please confirm delete');
        }

        $stmt = $db->prepare('DELETE FROM categories WHERE id=:id');
        $stmt->execute(array(':id'=>$_POST['id']));

        // delete all the challenges and all objects related to it
        $stmt = $db->prepare('SELECT id FROM challenges WHERE category = :id');
        $stmt->execute(array(':id' => $_POST['id']));
        while ($challenge = $stmt->fetch(PDO::FETCH_ASSOC)) {
            delete_challenge_cascading($challenge['id']);
        }

        header('location: manage.php?generic_success=1');
        exit();
    }
}

validate_id($_GET['id']);

$stmt = $db->prepare('SELECT * FROM categories WHERE id = :id');
$stmt->execute(array(':id' => $_GET['id']));
$category = $stmt->fetch(PDO::FETCH_ASSOC);

head('Site management');
menu_management();

section_subhead('Edit category: ' . $category['title']);
echo '
<form class="form-horizontal" method="post">

  <div class="control-group">
      <label class="control-label" for="title">Title</label>
      <div class="controls">
          <input type="text" id="title" name="title" class="input-block-level" placeholder="Title" value="',htmlspecialchars($category['title']),'">
      </div>
  </div>

  <div class="control-group">
      <label class="control-label" for="description">Description</label>
      <div class="controls">
          <textarea id="description" name="description" class="input-block-level" rows="10">',htmlspecialchars($category['description']),'</textarea>
      </div>
  </div>

  <div class="control-group">
      <label class="control-label" for="available_from">Available from</label>
      <div class="controls">
          <input type="text" id="available_from" name="available_from" class="input-block-level" placeholder="Available from" value="',get_date_time($category['available_from']),'">
      </div>
  </div>

  <div class="control-group">
      <label class="control-label" for="available_until">Available until</label>
      <div class="controls">
          <input type="text" id="available_until" name="available_until" class="input-block-level" placeholder="Available until" value="',get_date_time($category['available_until']),'">
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

section_subhead('Delete category: ' . $category['title']);
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

  <div class="alert alert-error">Warning! This will delete all challenges under this category, as well as all submissions, files, and hints related those challenges!</div>

  <div class="control-group">
      <label class="control-label" for="delete"></label>
      <div class="controls">
          <button type="submit" id="delete" class="btn btn-danger">Delete category</button>
      </div>
  </div>
</form>
';

foot();