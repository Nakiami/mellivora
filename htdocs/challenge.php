<?php

require('../include/mellivora.inc.php');

validate_id($_GET['id']);

head('Challenge details');

if (cache_start('challenge_' . $_GET['id'], CONFIG_CACHE_TIME_CHALLENGE)) {

    $challenge = db_query_fetch_one('
        SELECT
           ch.title,
           ch.description,
           ch.available_from AS challenge_available_from,
           ca.title AS category_title,
           ca.available_from AS category_available_from
        FROM challenges AS ch
        LEFT JOIN categories AS ca ON ca.id = ch.category
        WHERE ch.id = :id',
        array('id'=>$_GET['id'])
    );

    if (empty($challenge)) {
        message_generic('Sorry', 'No challenge found with this ID', false);
    }

    $now = time();
    if ($challenge['challenge_available_from'] > $now || $challenge['category_available_from'] > $now) {
        message_generic('Sorry', 'This challenge is not yet available', false);
    }

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

    section_head($challenge['title']);

    $num_correct_solves = count($submissions);

    if (!$num_correct_solves) {
        echo 'This challenge has not yet been solved by any teams.';
    }

    else {
        $user_count = db_query_fetch_one('SELECT COUNT(*) AS num FROM users WHERE competing = 1');
        echo 'This challenge has been solved by ', (number_format((($num_correct_solves / $user_count['num']) * 100), 1)), '% of users.';

        echo '
       <table class="challenge-table table table-striped table-hover">
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
                <td class="team-name"><a href="user.php?id=', htmlspecialchars($submission['user_id']), '">', htmlspecialchars($submission['team_name']), '</a></td>
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