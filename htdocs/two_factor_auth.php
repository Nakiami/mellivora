<?php

require('../include/mellivora.inc.php');

prefer_ssl();

head(lang_get('two_factor_auth_required'));

section_head(lang_get('two_factor_auth_required'));
form_start('actions/two_factor_auth');
form_input_text('Code', false, array('autocomplete'=>'off', 'autofocus'=>true));
form_hidden('action', 'authenticate');
form_button_submit(lang_get('authenticate'));
form_end();

foot();