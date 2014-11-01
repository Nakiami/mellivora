<?php

function json_error($message) {
    return json_encode(array('error'=>htmlspecialchars($message)));
}

function json_scoreboard ($user_type = null) {

    $values = array();

    if (is_valid_id($user_type)) {
        $values['user_type'] = $user_type;
    }

    $scores = db_query_fetch_all('
        SELECT
           u.id AS user_id,
           u.team_name,
           co.country_code,
           SUM(c.points) AS score,
           MAX(s.added) AS tiebreaker
        FROM users AS u
        LEFT JOIN countries AS co ON co.id = u.country_id
        LEFT JOIN submissions AS s ON u.id = s.user_id AND s.correct = 1
        LEFT JOIN challenges AS c ON c.id = s.challenge
        WHERE
          u.competing = 1
          '.(is_valid_id($user_type) ? 'AND u.user_type = :user_type' : '').'
        GROUP BY u.id
        ORDER BY score DESC, tiebreaker ASC',
        $values
    );

    $scoreboard = array();
    for ($i = 0; $i < count($scores); $i++) {
        $scoreboard['standings'][$i] = array(
            'pos'=>($i+1),
            'team'=>$scores[$i]['team_name'],
            'score'=>array_get($scores[$i], 'score', 0),
            'country'=>$scores[$i]['country_code']
        );
    }

    echo json_encode($scoreboard);
}