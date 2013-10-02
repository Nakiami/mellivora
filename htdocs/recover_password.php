<?php

define('IN_FILE', true);
require('../include/general.inc.php');

message_generic('Bad luck brian', 'Joins computer security competition, can\'t remember password.');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

       if ($_POST['action'] == 'reset_password') {
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

section_subhead('Reset password');
echo '
<form class="form-horizontal"  method="post">

    <input name="',md5(CONFIG_SITE_NAME.'USR'),'" type="text" class="input-block-level" placeholder="Email address">
    <input type="hidden" name="action" value="reset_password" />

    <div class="control-group">
        <label class="control-label" for="reset_password"></label>
        <div class="controls">
            <button type="submit" id="reset_password" class="btn btn-danger">Reset password</button>
        </div>
    </div>
</form>
';

foot();