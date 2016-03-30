<?php

require('../../include/mellivora.inc.php');

enforce_authentication(CONST_USER_CLASS_MODERATOR);

validate_id($_GET['id']);

head('Site management');
menu_management();
section_subhead('Edit dynamic menu item');

$menu_item = db_select_one(
    'dynamic_menu',
    array('*'),
    array('id' => $_GET['id'])
);

form_start(CONFIG_SITE_ADMIN_RELPATH . 'actions/edit_dynamic_menu_item');

form_input_text('Title', $menu_item['title']);
form_input_text('Permalink', $menu_item['permalink']);

dynamic_visibility_select($menu_item['visibility']);

$pages = db_select_all(
    'dynamic_pages',
    array(
        'id',
        'title'
    )
);
array_unshift($pages, array('id'=>0,'title'=>'--- No internal link ---'));
form_select($pages, 'Internal page', 'id', $menu_item['internal_page'], 'title');

user_class_select($menu_item['min_user_class']);

form_input_text('URL', $menu_item['url']);

form_input_text('Priority', $menu_item['priority']);

form_hidden('action', 'edit');
form_hidden('id', $_GET['id']);
form_button_submit('Save changes');
form_end();

section_subhead('Delete menu item');
form_start(CONFIG_SITE_ADMIN_RELPATH . 'actions/edit_dynamic_menu_item');
form_input_checkbox('Delete confirmation');
form_hidden('action', 'delete');
form_hidden('id', $_GET['id']);
form_button_submit('Delete menu item', 'danger');
form_end();

foot();
