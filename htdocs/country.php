<?php

require('../include/mellivora.inc.php');

login_session_refresh();

if (strlen(array_get($_GET, 'code')) != 2) {
    message_error('Please supply a valid country code');
}

$country = db_select_one(
    'countries',
    array(
        'id',
        'country_name',
        'country_code'
    ),
    array(
        'country_code'=>$_GET['code']
    )
);

if (!$country) {
    message_error('No country found with that code');
}

head($country['country_name']);

if (cache_start('country_' . $_GET['code'], CONFIG_CACHE_TIME_COUNTRIES)) {

    section_head(htmlspecialchars($country['country_name']) . country_flag_link($country['country_name'], $country['country_code'], true), '', false);

    $scores = db_query_fetch_all('
            SELECT
               u.id AS user_id,
               u.team_name,
               u.competing,
               co.id AS country_id,
               co.country_name,
               co.country_code,
               SUM(c.points) AS score,
               MAX(s.added) AS tiebreaker
            FROM users AS u
            LEFT JOIN countries AS co ON co.id = u.country_id
            LEFT JOIN submissions AS s ON u.id = s.user_id AND s.correct = 1
            LEFT JOIN challenges AS c ON c.id = s.challenge
            WHERE u.competing = 1 AND co.id = :country_id
            GROUP BY u.id
            ORDER BY score DESC, tiebreaker ASC',
        array(
            'country_id'=>$country['id']
        )
    );

    scoreboard($scores);

    cache_end('country_' . $_GET['code']);
}

foot();