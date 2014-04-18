<?php

require('../include/mellivora.inc.php');

validate_id($_GET['id']);

head('Challenge details');

$cache = new Cache_Lite_Output(array('cacheDir' => CONFIG_PATH_CACHE, 'lifeTime' => CONFIG_CACHE_TIME_CHALLENGE));
if (!($cache->start('challenge_' . $_GET['id']))) {

    $submissions = db_query(
        'SELECT
            u.id AS user_id,
            u.team_name,
            s.added,
            c.available_from
          FROM users AS u
          LEFT JOIN submissions AS s ON s.user_id = u.id
          LEFT JOIN challenges AS c ON c.id = s.challenge
          WHERE s.challenge=:id AND s.correct = 1
          ORDER BY s.added ASC',
        array('id' => $_GET['id'])
    );

    if (!sizeof($submissions)) {
        message_generic("Submissions", "This challenge has not yet been solved by any teams!", false);
    }

    $stmt = $db->prepare('SELECT
                            ch.title,
                            ch.description,
                            ca.title AS category_title
                          FROM challenges AS ch
                          LEFT JOIN categories AS ca ON ca.id = ch.category
                          WHERE ch.id=:id
                          ');
    $stmt->execute(array('id' => $_GET['id']));
    $challenge = $stmt->fetch(PDO::FETCH_ASSOC);

    section_head($challenge['title']);

    $stmt = $db->query('SELECT COUNT(*) AS num FROM users');
    $user_count = $stmt->fetch(PDO::FETCH_ASSOC);

    echo 'This challenge has been solved by ', (number_format((($stmt->rowCount() / $user_count['num']) * 100), 1)), '% of users.';

    echo '
   <table class="table table-striped table-hover">
   <thead>
   <tr>
     <th>Position</th>
     <th>Challenge</th>
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
            <td>', time_elapsed($submission['added'], $submission['available_from']), ' after release, ', time_elapsed($submission['added']), ' ago (', date_time($submission['added']), ')</td>
          </tr>
          ';
        $i++;
    }

    echo '
   </tbody>
   </table>
     ';

    $cache->end();
}

foot();