<?php

require('../include/mellivora.inc.php');

prefer_ssl();

head('Two-factor authentication required');

section_head('Two-factor authentication required');
form_start('actions/two_factor_auth');
form_input_text('Code', false, array('autocomplete'=>'off', 'autofocus'=>true));
form_hidden('action', 'authenticate');
form_button_submit('Authenticate');
form_end();

foot();