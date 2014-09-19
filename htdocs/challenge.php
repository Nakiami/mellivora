<?php

require('../include/mellivora.inc.php');

validate_id($_GET['id']);

head('Challenge details');

if (cache_start('challenge_' . $_GET['id'], CONFIG_CACHE_TIME_CHALLENGE)) {

    $submissions = db_query_fetch_all(
        'SELECT
            u.id AS user_id,
            u.team_name,
            s.added,
            c.available_from
          FROM users AS u
          LEFT JOIN submissions AS s ON s.user_id = u.id
          LEFT JOIN challenges AS c ON c.id = s.challenge
          WHERE
             u.competing = 1 AND
             s.challenge = :id AND
             s.correct = 1
          ORDER BY s.added ASC',
        array('id' => $_GET['id'])
    );

    $challenge = db_query_fetch_one('
        SELECT
           ch.title,
           ch.description,
           ca.title AS category_title
        FROM challenges AS ch
        LEFT JOIN categories AS ca ON ca.id = ch.category
        WHERE ch.id = :id',
        array('id'=>$_GET['id'])
    );

    section_head($challenge['title']);

    $num_correct_solves = count($submissions);

    if (!$num_correct_solves) {
        echo 'This challenge has not yet been solved by any teams.';
    }

    else {
        $user_count = db_query_fetch_one('SELECT COUNT(*) AS num FROM users WHERE competing = 1');
        echo 'This challenge has been solved by ', (number_format((($num_correct_solves / $user_count['num']) * 100), 1)), '% of users.';

        echo '
       <table class="table table-striped table-hover">
       <thead>
       <tr>
         <th>Position</th>
         <th>Team</th>
         <th>Solved</th>
       </tr>
       </thead>
       <tbody>
       ';
        $i = 1;
        foreach ($submissions as $submission) {
            echo '
              <tr>
                <td>', number_format($i), ' ', get_position_medal($i), '</td>
                <td><a href="user.php?id=', htmlspecialchars($submission['user_id']), '">', htmlspecialchars($submission['team_name']), '</a></td>
                <td>', time_elapsed($submission['added'], $submission['available_from']), ' after release (', date_time($submission['added']), ')</td>
              </tr>
              ';
            $i++;
        }

        echo '
       </tbody>
       </table>
         ';
    }

    cache_end('challenge_' . $_GET['id']);
}

foot();