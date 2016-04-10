<?php

require('../../include/mellivora.inc.php');

enforce_authentication(CONST_USER_CLASS_MODERATOR);

validate_id($_GET['id']);

$category = db_select_one(
    'categories',
    array('*'),
    array('id' => $_GET['id'])
);

if (empty($category)) {
    message_error('No category found with this ID');
}

head('Site management');
menu_management();

section_subhead('Edit category: ' . $category['title']);
form_start(CONFIG_SITE_ADMIN_RELPATH . 'actions/edit_category');
form_input_text('Title', $category['title']);
form_textarea('Description', $category['description']);
form_input_checkbox('Exposed', $category['exposed']);
form_input_text('Available from', date_time($category['available_from']));
form_input_text('Available until', date_time($category['available_until']));
form_hidden('action', 'edit');
form_hidden('id', $_GET['id']);
form_button_submit('Save changes');
form_end();

section_subhead('Delete category: ' . $category['title']);
form_start(CONFIG_SITE_ADMIN_RELPATH . 'actions/edit_category');
form_input_checkbox('Delete confirmation');
form_hidden('action', 'delete');
form_hidden('id', $_GET['id']);
message_inline_red('Warning! This will delete all challenges under this category, as well as all submissions, files, and hints related those challenges!');
form_button_submit('Delete category', 'danger');
form_end();

foot();