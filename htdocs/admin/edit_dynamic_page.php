<?php

require('../../include/mellivora.inc.php');

enforce_authentication(CONST_USER_CLASS_MODERATOR);

validate_id($_GET['id']);

$page = db_select_one(
    'dynamic_pages',
    array('*'),
    array('id' => $_GET['id'])
);

head('Site management');
menu_management();

section_subhead('Edit dynamic page: ' . $page['title']);
form_start(CONFIG_SITE_ADMIN_RELPATH . 'actions/edit_dynamic_page');
form_input_text('Title', $page['title']);
form_textarea('Body', $page['body']);

dynamic_visibility_select($page['visibility']);

user_class_select($page['min_user_class']);

form_hidden('action', 'edit');
form_hidden('id', $_GET['id']);

form_button_submit('Save changes');
form_bbcode_manual();
form_end();

section_subhead('Delete');
form_start(CONFIG_SITE_ADMIN_RELPATH . 'actions/edit_dynamic_page');
form_input_checkbox('Delete confirmation');
form_hidden('action', 'delete');
form_hidden('id', $_GET['id']);
form_button_submit('Delete', 'danger');
form_end();

foot();