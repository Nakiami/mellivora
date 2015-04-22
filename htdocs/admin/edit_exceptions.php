<?php

require('../../include/mellivora.inc.php');

enforce_authentication(CONST_USER_CLASS_MODERATOR);

head('Exceptions');
menu_management();

section_subhead('Clear exceptions');
form_start(CONFIG_SITE_ADMIN_RELPATH . 'actions/edit_exceptions');
form_input_checkbox('Delete confirmation');
form_hidden('action', 'delete');
message_inline_red('Warning! This will delete ALL exception logs!!');
form_button_submit('Clear exceptions', 'danger');
form_end();

foot();
