<?php

require('../../include/mellivora.inc.php');

enforce_authentication(CONST_USER_CLASS_MODERATOR);

head('Site management');
menu_management();
section_subhead('New user type');

message_inline_blue('User types are not required. You can add them if you wish to group contestants and give them separate scoreboards. If not, everyone will be in the same group.');

form_start(CONFIG_SITE_ADMIN_RELPATH . 'actions/new_user_type');
form_input_text('Title');
form_textarea('Description');
form_hidden('action', 'new');
form_button_submit('Create new user type');
form_end();

foot();