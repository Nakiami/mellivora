<?php

require('../../include/mellivora.inc.php');

enforce_authentication(CONST_USER_CLASS_MODERATOR);

head('Site management');
menu_management();
section_subhead('New challenge');

form_start(CONFIG_SITE_ADMIN_RELPATH . 'actions/new_challenge');
form_input_text('Title');
form_textarea('Description');

form_textarea('Flag');
form_input_checkbox('Automark', true);
form_input_checkbox('Case insensitive');

form_input_text('Points');
form_input_text('Num attempts allowed');
form_input_text('Min seconds between submissions');

$opts = db_query_fetch_all('SELECT * FROM categories ORDER BY title');
form_select($opts, 'Category', 'id', $_GET['category'], 'title');

form_input_checkbox('Public', true);
form_input_text('Available from', date_time());
form_input_text('Available until', date_time());

message_inline_blue('Create and edit challenge to add files.');

form_hidden('action', 'new');

form_button_submit('Create challenge');
form_end();

foot();