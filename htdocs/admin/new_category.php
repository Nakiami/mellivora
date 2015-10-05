<?php

require('../../include/mellivora.inc.php');

enforce_authentication(CONST_USER_CLASS_MODERATOR);

head('Site management');
menu_management();

section_subhead('New category');
form_start(CONFIG_SITE_ADMIN_RELPATH . 'actions/new_category');
form_input_text('Title');
form_textarea('Description');
form_input_checkbox('Exposed', true);
form_input_text('Available from', date_time());
form_input_text('Available until', date_time());
form_hidden('action', 'new');
form_button_submit('Create category');
form_end();

foot();