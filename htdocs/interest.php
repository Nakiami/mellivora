<?php

define('IN_FILE', true);
require('../include/general.inc.php');

forceSSL();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($_POST['action'] == 'register') {

        if (CONFIG_RECAPTCHA_ENABLE) {
            checkCaptcha($_POST);
        }

        validateEmail($_POST['email']);

        $stmt = $db->prepare('SELECT id FROM interest WHERE email=:email');
        $stmt->execute(array(':email' => $_POST['email']));
        $interest = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($interest['id']) {
            errorMessage('You have already registered your interest!');
        }

        $stmt = $db->prepare('
        INSERT INTO interest
        (
        added,
        name,
        email,
        secret
        )
        VALUES (
        UNIX_TIMESTAMP(),
        :name,
        :email,
        :secret
        )
        ');

        $stmt->execute(array(
            ':name'=>$_POST['name'],
            ':email'=>$_POST['email'],
            ':secret'=>generateRandomString(40, false)
        ));

        if ($db->lastInsertId()) {
            genericMessage('Success', 'The email '.$_POST['email'].' has been registered. We look forward to seeing you in our next competition!');
        } else {
            errorMessage('Could not register interest. You must not be interested enough!');
        }
    }
}

head('Register interest');

echo '
<form method="post" class="form-signin">
    <h2 class="form-signin-heading">Register interest</h2>
    <p>
        The Mellivora team are likely to run more CTFs in the future. These will most likely be open to the public.
        Input your email below if you\'re interested in hearing from us about future competitions.
        We won\'t spam you. Your email address won\'t be shared with third parties.
    </p>
    <input name="name" type="text" class="input-block-level" placeholder="Name / team name / nick">
    <input name="email" type="text" class="input-block-level" placeholder="Email address">';

if (CONFIG_RECAPTCHA_ENABLE) {
    displayCaptcha();
}

echo '
    <input type="hidden" name="action" value="register" />
    <button class="btn btn-primary" type="submit">Register interest</button>
</form>
';

foot();