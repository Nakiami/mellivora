<?php

require('../../include/mellivora.inc.php');

enforce_authentication(CONST_USER_CLASS_MODERATOR);

head('Site management');
menu_management();
section_subhead('New dynamic menu item');

form_start(CONFIG_SITE_ADMIN_RELPATH . 'actions/new_dynamic_menu_item');

form_input_text('Title');
form_input_text('Permalink');

dynamic_visibility_select();

$pages = db_select_all(
    'dynamic_pages',
    array(
        'id',
        'title'
    )
);
array_unshift($pages, array('id'=>0,'title'=>'--- No internal link ---'));
form_select($pages, 'Internal page', 'id', null, 'title');

user_class_select();

form_input_text('URL');

form_input_text('Priority');

form_hidden('action', 'new');
form_button_submit('Create');
form_end();

foot();