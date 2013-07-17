<?php

define('IN_FILE', true);
require('../include/general.inc.php');

enforceAuthentication(CONFIG_UC_MODERATOR);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if ($_POST['action'] == 'edit' && is_valid_id($_POST['id'])) {

        $stmt = $db->prepare('
        UPDATE users SET
        username=:username,
        team_name=:team_name,
        class=:class,
        enabled=:enabled
        WHERE id=:id
        ');

        $stmt->execute(array(
            ':username'=>$_POST['username'],
            ':team_name'=>$_POST['team_name'],
            ':class'=>$_POST['class'],
            ':enabled'=>$_POST['enabled'],
            ':id'=>$_POST['id']
        ));

        header('location: edit_user.php?id='.$_POST['id'].'&generic_success=1');
        exit();
    }

    else if ($_POST['action'] == 'delete' && is_valid_id($_POST['id'])) {

        if (!$_POST['delete_confirmation']) {
            errorMessage('Please confirm delete');
        }

        $stmt = $db->prepare('DELETE FROM users WHERE id=:id');
        $stmt->execute(array(':id'=>$_POST['id']));

        $stmt = $db->prepare('DELETE FROM submissions WHERE user=:id');
        $stmt->execute(array(':id'=>$_POST['id']));

        header('location: list_users.php?generic_success=1');
        exit();
    }
}

if (is_valid_id($_GET['id'])) {

    $stmt = $db->prepare('SELECT * FROM users WHERE id = :id');
    $stmt->execute(array(':id' => $_GET['id']));
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    head('Site management');
    sectionSubHead('Edit challenge: ' . $user['title']);

    echo '
    <form class="form-horizontal" method="post">

        <div class="control-group">
            <label class="control-label" for="username">Username</label>
            <div class="controls">
                <input type="text" id="username" name="username" class="input-block-level" placeholder="Username" value="',htmlspecialchars($user['username']),'">
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

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Save changes</button>
        </div>

    </form>';

    sectionSubHead('Delete user: ' . $user['title']);

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

        <div class="form-actions">
            <button type="submit" class="btn btn-danger">Delete user</button>
        </div>
    </form>
    ';
}

foot();