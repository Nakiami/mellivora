<?php

require('../include/general.inc.php');

enforce_authentication(CONFIG_UC_MODERATOR);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    validate_id($_POST['id']);

    if ($_POST['action'] == 'edit') {

        validate_email($_POST['email']);

        db_update(
          'users',
          array(
             'email'=>$_POST['email'],
             'team_name'=>$_POST['team_name'],
             'class'=>$_POST['class'],
             'enabled'=>($_POST['enabled'] ? 1 : 0),
             'priority'=>$_POST['priority']
          ),
          array(
             'id'=>$_POST['id']
          )
        );

        header('location: list_users.php?generic_success=1');
        exit();
    }

    else if ($_POST['action'] == 'delete') {

        if (!$_POST['delete_confirmation']) {
            message_error('Please confirm delete');
        }

        $stmt = $db->prepare('DELETE FROM users WHERE id=:id');
        $stmt->execute(array(':id'=>$_POST['id']));

        $stmt = $db->prepare('DELETE FROM submissions WHERE user_id=:id');
        $stmt->execute(array(':id'=>$_POST['id']));

        $stmt = $db->prepare('DELETE FROM ip_log WHERE user_id=:id');
        $stmt->execute(array(':id'=>$_POST['id']));

        header('location: list_users.php?generic_success=1');
        exit();
    }

    else if ($_POST['action'] == 'reset_password') {
        $new_password = generate_random_string(8, false);
        $new_salt = make_salt();

        $new_passhash = make_passhash($new_password, $new_salt);

        $stmt = $db->prepare('
        UPDATE users SET
        salt=:salt,
        passhash=:passhash
        WHERE id=:id
        ');
        $stmt->execute(array(':passhash'=>$new_passhash, ':salt'=>$new_salt, ':id'=>$_POST['id']));

        message_generic('Success', 'Users new password is: ' . $new_password);
    }
}

validate_id($_GET['id']);

$stmt = $db->prepare('SELECT * FROM users WHERE id = :id');
$stmt->execute(array(':id' => $_GET['id']));
$user = $stmt->fetch(PDO::FETCH_ASSOC);

head('Site management');
menu_management();

section_subhead('Edit user: ' . $user['team_name']);
echo '
<form class="form-horizontal" method="post">

  <div class="control-group">
      <label class="control-label" for="email">Email</label>
      <div class="controls">
          <input type="text" id="email" name="email" class="input-block-level" placeholder="Email" value="',htmlspecialchars($user['email']),'">
      </div>
  </div>

  <div class="control-group">
      <label class="control-label" for="team_name">Team name</label>
      <div class="controls">
          <input type="text" id="team_name" name="team_name" class="input-block-level" placeholder="Team name" value="',htmlspecialchars($user['team_name']),'">
      </div>
  </div>

  <div class="control-group">
      <label class="control-label" for="enabled">Enabled</label>
      <div class="controls">
          <input type="checkbox" id="enabled" name="enabled" class="input-block-level" value="1"',($user['enabled'] ? ' checked="checked"' : ''),'>
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

section_subhead('Reset password');
echo '
<form class="form-horizontal"  method="post">
  <div class="control-group">
      <label class="control-label" for="reset_confirmation">Reset users password</label>

      <div class="controls">
          <input type="checkbox" id="reset_confirmation" name="reset_confirmation" value="1" />
      </div>
  </div>

  <input type="hidden" name="action" value="reset_password" />
  <input type="hidden" name="id" value="',htmlspecialchars($_GET['id']),'" />

  <div class="control-group">
      <label class="control-label" for="reset_password"></label>
      <div class="controls">
          <button type="submit" id="reset_password" class="btn btn-danger">Reset password</button>
      </div>
  </div>
</form>
';

section_subhead('Delete user');
echo '
<form class="form-horizontal"  method="post">
  <div class="control-group">
      <label class="control-label" for="delete_confirmation">I want to delete this user.</label>

      <div class="controls">
          <input type="checkbox" id="delete_confirmation" name="delete_confirmation" value="1" />
      </div>
  </div>

  <input type="hidden" name="action" value="delete" />
  <input type="hidden" name="id" value="',htmlspecialchars($_GET['id']),'" />

  <div class="alert alert-error">Warning! This will delete all submissions made by this user!</div>

  <div class="control-group">
      <label class="control-label" for="delete"></label>
      <div class="controls">
          <button type="submit" id="delete" class="btn btn-danger">Delete user</button>
      </div>
  </div>
</form>
';

foot();