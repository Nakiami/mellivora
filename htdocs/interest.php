<?php

require('../include/mellivora.inc.php');

prefer_ssl();

head(lang_get('register_interest'));

section_head(lang_get('register_interest'));
message_inline_bland(lang_get('register_interest_text'));

form_start('actions/interest','form-signin');
echo '
    <input name="email" type="text" class="form-control" placeholder="',lang_get('email_address'),'">
    <input name="name" type="text" class="form-control" placeholder="',lang_get('name_nick'),'">';

if (CONFIG_RECAPTCHA_ENABLE_PUBLIC) {
    display_captcha();
}

form_hidden('action', 'register');
echo '
    <button class="btn btn-primary" type="submit">',lang_get('register_interest'),'</button>
    ';
form_end();

foot();