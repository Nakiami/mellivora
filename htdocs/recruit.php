<?php

require('../include/mellivora.inc.php');

prefer_ssl();

enforce_authentication();

head(lang_get('expression_of_interest'));

section_head(lang_get('expression_of_interest'));
message_inline_bland(lang_get('recruitment_text'));

form_start('actions/recruit','form-signin');
echo '
    <input name="name" type="text" class="form-control" placeholder="',lang_get('name_optional'),'">
    <input name="email" type="email" class="form-control" placeholder="',lang_get('email_address'),'" required>
    <input name="city" type="text" class="form-control" placeholder="',lang_get('city_optional'),'">
    ';

country_select();

form_hidden('action', 'register');
echo '
    <button class="btn btn-primary" type="submit">',lang_get('register_interest'),'</button>
    ';
form_end();

foot();