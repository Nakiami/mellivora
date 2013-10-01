<?php

define('IN_FILE', true);
require('../include/general.inc.php');

enforceAuthentication(CONFIG_UC_MODERATOR);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if ($_POST['action'] == 'new') {

       $id = dbInsert(
          'restrict_email',
          array(
             'added'=>time(),
             'added_by'=>$_SESSION['id'],
             'rule'=>$_POST['rule'],
             'white'=>($_POST['white'] ? 1 : 0),
             'priority'=>$_POST['priority'],
             'enabled'=>($_POST['enabled'] ? 1 : 0)
          )
       );

       if ($id) {
          header('location: list_restrict_email.php?generic_success=1');
          exit();
       } else {
          errorMessage('Could not insert new email restriction: '.$db->errorCode());
       }
    }
}

head('Site management');
managementMenu();
sectionSubHead('New signup rule');

echo '

<div class="alert alert-info">
    Rules in list below are applied top-down. Rules further down on the list override rules above.
    List is ordered by "priority". A higher "priority" value puts a rule further down the list.
    Rules must be of format: "xxx@yyy", "*@yyy", or "xxx@*".
</div>

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