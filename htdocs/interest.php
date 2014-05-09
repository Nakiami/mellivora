<?php

require('../include/mellivora.inc.php');

prefer_ssl();

head('Register interest');

section_head('Register interest');
message_inline_bland("We are likely to run more CTFs in the future. These will most likely be open to the public.
                      Input your email below if you're interested in hearing from us about future competitions.
                      We won't spam you. Your email address won't be shared with third parties.");

form_start('actions/interest','form-signin');
echo '
    <input name="email" type="text" class="form-control" placeholder="Email address">
    <input name="name" type="text" class="form-control" placeholder="Name / team name / nick">';

if (CONFIG_RECAPTCHA_ENABLE_PUBLIC) {
    display_captcha();
}

form_hidden('action', 'register');
echo '
    <button class="btn btn-primary" type="submit">Register interest</button>
    ';
form_end();

foot();