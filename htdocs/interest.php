<?php

require('../include/general.inc.php');

force_ssl();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($_POST['action'] == 'register') {

        if (CONFIG_RECAPTCHA_ENABLE) {
            check_captcha($_POST);
        }

        validate_email($_POST['email']);

        $stmt = $db->prepare('SELECT id FROM interest WHERE email=:email');
        $stmt->execute(array(':email' => $_POST['email']));
        $interest = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($interest['id']) {
            message_error('You have already registered your interest!');
        }

        $id = db_insert(
            'interest',
            array(
                'added'=>time(),
                'name'=>$_POST['name'],
                'email'=>$_POST['email'],
                'secret'=>generate_random_string(40, false)
            )
        );

        if ($id) {
            message_generic('Success', 'The email '.htmlspecialchars($_POST['email']).' has been registered. We look forward to seeing you in our next competition!');
        } else {
            message_error('Could not register interest. You must not be interested enough!');
        }
    }
}

head('Register interest');

section_head('Register interest');
message_inline_bland('The Mellivora team are likely to run more CTFs in the future. These will most likely be open to the public.
                      Input your email below if you\'re interested in hearing from us about future competitions.
                      We won\'t spam you. Your email address won\'t be shared with third parties.');

form_start('','form-signin');
echo '
    <input name="name" type="text" class="input-block-level" placeholder="Name / team name / nick">
    <input name="email" type="text" class="input-block-level" placeholder="Email address">';

if (CONFIG_RECAPTCHA_ENABLE) {
    display_captcha();
}

form_hidden('action', 'register');
echo '
    <button class="btn btn-primary" type="submit">Register interest</button>
    ';
form_end();

foot();