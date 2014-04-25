<?php

require('../include/mellivora.inc.php');

enforce_authentication();

$user = db_select_one(
    'users',
    array(
        'team_name',
        'email',
        'enabled',
        'competing',
        'country_id'
    ),
    array('id' => $_SESSION['id'])
);

head('Profile');

section_subhead('Profile settings', '| <a href="user?id='.htmlspecialchars($_SESSION['id']).'">View public profile</a>', false);

form_start('actions/profile');
form_input_text('Email', $user['email'], true);
form_input_text('Team name', $user['team_name'], true);

$opts = db_query_fetch_all('SELECT * FROM countries ORDER BY country_name ASC');
form_select($opts, 'Country', 'id', $user['country_id'], 'country_name');

form_hidden('action', 'edit');
form_button_submit('Save changes');
form_end();

section_subhead('Reset password');
form_start('actions/profile');
form_input_password('Password');
form_input_password('Password Again');
form_hidden('action', 'reset_password');
form_button_submit('Reset password', 'warning');
form_end();

foot();