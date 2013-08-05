<?php

define('IN_FILE', true);
require('../include/general.inc.php');
enforceAuthentication();

head('User details');

if (isValidID($_GET['id'])) {

    $stmt = $db->prepare('SELECT team_name FROM users WHERE id=:user_id');
    $stmt->execute(array('user_id'=>$_GET['id']));
    $submission = $stmt->fetch(PDO::FETCH_ASSOC);

    sectionHead($submission['team_name']);

    $stmt = $db->prepare('
        SELECT
        ca.title,
        (SELECT SUM(ch.points) FROM challenges AS ch JOIN submissions AS s ON s.challenge = ch.id AND s.user_id = :user_id AND s.correct = 1 WHERE ch.category = ca.id GROUP BY ch.category) AS points,
        (SELECT SUM(ch.points) FROM challenges AS ch WHERE ch.category = ca.id GROUP BY ch.category) AS category_total
        FROM categories AS ca
        ORDER BY ca.title ASC
        ');
    $stmt->execute(array(':user_id' => $_GET['id']));

    $user_total = 0;
    $ctf_total = 0;
    while($challenge = $stmt->fetch(PDO::FETCH_ASSOC)) {

        echo htmlspecialchars($challenge['title']), ' ', number_format($challenge['points']) ,' / ', number_format($challenge['category_total']), ' (', round(($challenge['points']/$challenge['category_total'])*100), '%)';

        echo '
        <div class="',($challenge['points'] == $challenge['category_total'] ? 'progress progress-success progress-striped' : 'progress progress-striped'),'">
        <div class="bar" style="width: ',(( $challenge['points']/$challenge['category_total'] ) * 100),'%;"></div>
        </div>
    ';

        $user_total += $challenge['points'];
        $ctf_total += $challenge['category_total'];
    }

    echo 'Total: ', number_format($user_total), ' / ', number_format($ctf_total), ' (', round(($user_total/$ctf_total)*100, 1), '%)';

    sectionSubHead('Solved challenges');

    $stmt = $db->prepare('
    SELECT
    s.added,
    ch.available_from,
    ch.title,
    ch.points,
    ca.title AS category_title
    FROM submissions AS s
    LEFT JOIN challenges AS ch ON ch.id = s.challenge
    LEFT JOIN categories AS ca ON ca.id = ch.category
    WHERE
    s.correct = 1 AND
    s.user_id=:user_id
    ORDER BY s.added DESC
    ');
    $stmt->execute(array('user_id'=>$_GET['id']));

    echo '
    <table class="table table-striped table-hover">
      <thead>
        <tr>
          <th>Challenge</th>
          <th>Solved</th>
          <th>Points</th>
        </tr>
      </thead>
      <tbody>
     ';

    while ($submission = $stmt->fetch(PDO::FETCH_ASSOC)) {

        echo '
            <tr>
              <td>', htmlspecialchars($submission['title']),' (',htmlspecialchars($submission['category_title']),')</td>
              <td>', getTimeElapsed($submission['added'], $submission['available_from']),' after release (',getDateTime($submission['added']),')</td>
              <td>', number_format($submission['points']),'</td>
            </tr>
            ';

    }

    echo '
      </tbody>
    </table>
        ';
}

foot();