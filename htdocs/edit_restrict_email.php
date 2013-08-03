<?php

define('IN_FILE', true);
require('../include/general.inc.php');

enforceAuthentication(CONFIG_UC_MODERATOR);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if ($_POST['action'] == 'edit' && isValidID($_POST['id'])) {

        $stmt = $db->prepare('
        UPDATE restrict_email SET
        rule=:rule,
        enabled=:enabled,
        white=:white,
        priority=:priority
        WHERE id=:id
        ');

        $stmt->execute(array(
            ':rule'=>$_POST['rule'],
            ':enabled'=>$_POST['enabled'],
            ':white'=>$_POST['white'],
            ':priority'=>$_POST['priority'],
            ':id'=>$_POST['id']
        ));

        header('location: list_restrict_email.php?generic_success=1');
        exit();
    }

    else if ($_POST['action'] == 'delete' && isValidID($_POST['id'])) {

        if (!$_POST['delete_confirmation']) {
            errorMessage('Please confirm delete');
        }

        $stmt = $db->prepare('DELETE FROM restrict_email WHERE id=:id');
        $stmt->execute(array(':id'=>$_POST['id']));

        header('location: list_restrict_email.php?generic_success=1');
        exit();
    }
}

if (isValidID($_GET['id'])) {

    $stmt = $db->prepare('SELECT rule, enabled, white, priority FROM restrict_email WHERE id = :id');
    $stmt->execute(array(':id' => $_GET['id']));
    $rule = $stmt->fetch(PDO::FETCH_ASSOC);

    head('Site management');
    managementMenu();
    sectionSubHead('Edit signup rule');

    echo '
    <form class="form-horizontal" method="post">

        <div class="control-group">
            <label class="control-label" for="rule">Rule</label>
            <div class="controls">
                <input type="text" id="rule" name="rule" class="input-block-level" placeholder="Rule" value="',htmlspecialchars($rule['rule']),'">
            </div>
        </div>

        <div class="control-group">
            <label class="control-label" for="rule">Priority</label>
            <div class="controls">
                <input type="text" id="priority" name="priority" class="input-block-level" placeholder="Priority" value="',htmlspecialchars($rule['priority']),'">
            </div>
        </div>

        <div class="control-group">
            <label class="control-label" for="white">Whitelist</label>
            <div class="controls">
                <input type="checkbox" id="white" name="white" class="input-block-level" value="1"',($rule['white'] ? ' checked="checked"' : ''),'>
            </div>
        </div>

        <div class="control-group">
            <label class="control-label" for="enabled">Enabled</label>
            <div class="controls">
                <input type="checkbox" id="enabled" name="enabled" class="input-block-level" value="1"',($rule['enabled'] ? ' checked="checked"' : ''),'>
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

    sectionSubHead('Delete rule');

    echo '
    <form class="form-horizontal"  method="post">
        <div class="control-group">
            <label class="control-label" for="delete_confirmation">I want to delete this rule.</label>

            <div class="controls">
                <input type="checkbox" id="delete_confirmation" name="delete_confirmation" value="1" />
            </div>
        </div>

        <input type="hidden" name="action" value="delete" />
        <input type="hidden" name="id" value="',htmlspecialchars($_GET['id']),'" />

        <div class="control-group">
            <label class="control-label" for="delete"></label>
            <div class="controls">
                <button type="submit" id="delete" class="btn btn-danger">Delete rule</button>
            </div>
        </div>
    </form>
    ';
}

foot();