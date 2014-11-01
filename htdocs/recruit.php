<?php

require('../include/mellivora.inc.php');

prefer_ssl();

enforce_authentication();

head('Expression of interest');

section_head('Expression of interest');
message_inline_bland("Like the look of our sponsors? They're all hiring. Please fill out the form below if you wish to be contacted with recruitment information. Each team member can fill out the form individually. We won't share your details with anyone but our sponsors. We won't spam you. Only addresses entered into this form will be shared.");

form_start('actions/recruit','form-signin');
echo '
    <input name="name" type="text" class="form-control" placeholder="Name (optional)">
    <input name="email" type="email" class="form-control" placeholder="Email address" required>
    <input name="city" type="text" class="form-control" placeholder="City (optional)">
    ';

country_select();

form_hidden('action', 'register');
echo '
    <button class="btn btn-primary" type="submit">Register interest</button>
    ';
form_end();

foot();