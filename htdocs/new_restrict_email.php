<?php

require('../include/mellivora.inc.php');

enforce_authentication(CONFIG_UC_MODERATOR);

head('Site management');
menu_management();

section_subhead('New signup rule');

message_inline_info('Rules in list below are applied top-down. Rules further down on the list override rules above.
                     List is ordered by "priority". A higher "priority" value puts a rule further down the list.
                     Rules are PCRE regex. Example: ^.+@.+$');

form_start('new_restrict_email');
form_input_text('Rule');
form_input_text('Priority');
form_input_checkbox('Whitelist');
form_input_checkbox('Enabled');
form_hidden('action', 'new');
form_button_submit('Create new rule');
form_end();

foot();