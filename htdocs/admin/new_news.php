<?php

require('../../include/mellivora.inc.php');

enforce_authentication(CONFIG_UC_MODERATOR);

head('Site management');
menu_management();

section_subhead('New news post');
form_start(CONFIG_SITE_ADMIN_RELPATH . 'actions/new_news');
form_input_text('Title');
form_textarea('Body');
form_hidden('action', 'new');
form_button_submit('Publish news item');
form_bbcode_manual();
form_end();

foot();