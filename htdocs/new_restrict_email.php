<?php

define('IN_FILE', true);
require('../include/general.inc.php');

enforceAuthentication(CONFIG_UC_MODERATOR);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if ($_POST['action'] == 'new') {

        $stmt = $db->prepare('
        INSERT INTO restrict_email (
        added,
        added_by,
        rule,
        white,
        priority,
        enabled
        ) VALUES (
        UNIX_TIMESTAMP(),
        :user,
        :rule,
        :white,
        :priority,
        :enabled
        )
        ');

        $stmt->execute(array(
            ':user'=>$_SESSION['id'],
            ':rule'=>$_POST['rule'],
            ':white'=>$_POST['white'],
            ':priority'=>$_POST['priority'],
            ':enabled'=>$_POST['enabled']
        ));

        if ($db->lastInsertId()) {
            header('location: edit_restrict_email.php?id=' . $db->lastInsertId(). '&generic_success=1');
            exit();
        } else {
            errorMessage('Could not insert new rule: ' . $stmt->errorCode());
        }
    }
}

head('Site management');
managementMenu();
sectionSubHead('New signup rule');

echo '
<form class="form-horizontal" method="post">

    <div class="control-group">
        <label class="control-label" for="rule">Rule</label>
        <div class="controls">
            <input type="text" id="rule" name="rule" class="input-block-level" placeholder="Rule" />
        </div>
    </div>

    <div class="control-group">
        <label class="control-label" for="rule">Priority</label>
        <div class="controls">
            <input type="text" id="priority" name="priority" class="input-block-level" placeholder="Priority" />
        </div>
    </div>

    <div class="control-group">
        <label class="control-label" for="white">Whitelist</label>
        <div class="controls">
            <input type="checkbox" id="white" name="white" class="input-block-level" value="1" />
        </div>
    </div>

    <div class="control-group">
        <label class="control-label" for="enabled">Enabled</label>
        <div class="controls">
            <input type="checkbox" id="enabled" name="enabled" class="input-block-level" value="1" />
        </div>
    </div>

    <input type="hidden" name="action" value="new" />

    <div class="control-group">
        <label class="control-label" for="save"></label>
        <div class="controls">
            <button type="submit" id="save" class="btn btn-primary">Create rule</button>
        </div>
    </div>

</form>';

foot();