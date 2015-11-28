<?php

require('../../include/mellivora.inc.php');

enforce_authentication(CONST_USER_CLASS_MODERATOR);

validate_id(array_get($_GET, 'id'));

head(lang_get('user_details'));

$user = db_query_fetch_one('
    SELECT
        u.id,
        u.team_name,
        u.email,
        u.competing,
        co.country_name,
        co.country_code
    FROM users AS u
    LEFT JOIN countries AS co ON co.id = u.country_id
    WHERE
      u.id = :user_id',
    array('user_id' => $_GET['id'])
);

if (empty($user)) {
    message_generic(
        lang_get('sorry'),
        lang_get('no_user_found'),
        false);
}

section_head(
    htmlspecialchars($user['team_name']),
    country_flag_link(
        $user['country_name'], $user['country_code'], true) .
        button_link('Edit user', 'edit_user?id='.htmlspecialchars($user['id'])) . ' ' .
        button_link('Email user', 'new_email?to='.htmlspecialchars($user['email'])),
    false
);

if (!$user['competing']) {
    message_inline_blue(lang_get('non_competing_user'));
}

print_solved_graph($_GET['id']);

print_solved_challenges($_GET['id']);

print_user_ip_log($_GET['id'], 5);

print_user_submissions($_GET['id'], 5);

print_user_exception_log($_GET['id'], 5);

foot();