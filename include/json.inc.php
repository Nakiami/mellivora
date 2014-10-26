<?php

function json_error($message) {
    return json_encode(array('error'=>htmlspecialchars($message)));
}

function json_scoreboard () {

    // generate a json scoreboard
    // this function is so hacky..
    // could probably do with a rewrite

    $user_types = db_select_all(
        'user_types',
        array(
            'id',
            'title AS category'
        )
    );

    if (empty($user_types)) {
        $user_types = array(
            array(
                'id'=>0,
                'category'=>'all'
            )
        );
    }

    for ($i=0;$i<count($user_types);$i++) {
        $scores = db_query_fetch_all('
            SELECT
               u.id AS user_id,
               u.team_name,
               u.competing,
               co.country_code,
               SUM(c.points) AS score,
               MAX(s.added) AS tiebreaker
            FROM users AS u
            LEFT JOIN countries AS co ON co.id = u.country_id
            LEFT JOIN submissions AS s ON u.id = s.user_id AND s.correct = 1
            LEFT JOIN challenges AS c ON c.id = s.challenge
            WHERE u.competing = 1 AND u.user_type = :user_type
            GROUP BY u.id
            ORDER BY score DESC, tiebreaker ASC',
            array(
                'user_type'=>$user_types[$i]['id']
            )
        );

        unset($user_types[$i]['id']);

        for ($j=0; $j<count($scores); $j++) {
            $user_types[$i]['teams'][htmlspecialchars($scores[$j]['team_name'])] = array(
                'position'=>($j+1),
                'score'=>array_get($scores[$j], 'score', 0),
                'country'=>$scores[$j]['country_code']
            );
        }
    }

    echo json_encode($user_types);
}