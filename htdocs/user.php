<?php

require('../include/mellivora.inc.php');

validate_id(array_get($_GET, 'id'));

head(lang_get('user_details'));

if (cache_start(CONST_CACHE_NAME_USER . $_GET['id'], CONFIG_CACHE_TIME_USER)) {

    $user = db_query_fetch_one('
        SELECT
            u.team_name,
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
            false
        );
    }

    section_head(htmlspecialchars($user['team_name']), country_flag_link($user['country_name'], $user['country_code'], true), false);

    if (!$user['competing']) {
        message_inline_blue(lang_get('non_competing_user'));
    }

    print_solved_graph($_GET['id']);

    print_solved_challenges($_GET['id']);

    cache_end(CONST_CACHE_NAME_USER . $_GET['id']);
}

foot();