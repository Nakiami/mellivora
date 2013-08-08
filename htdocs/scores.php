<?php

define('IN_FILE', true);
require('../include/general.inc.php');

head('Scoreboard');

if ($_SESSION['id']) {

    echo '<div class="page-header"><h2>Your progress</h2></div>';

    $stmt = $db->prepare('
        SELECT
        ca.title,
        (SELECT SUM(ch.points) FROM challenges AS ch JOIN submissions AS s ON s.challenge = ch.id AND s.user_id = :user_id AND s.correct = 1 WHERE ch.category = ca.id GROUP BY ch.category) AS points,
        (SELECT SUM(ch.points) FROM challenges AS ch WHERE ch.category = ca.id GROUP BY ch.category) AS category_total
        FROM categories AS ca
        ORDER BY ca.title ASC
        ');
    $stmt->execute(array(':user_id' => $_SESSION['id']));

    $user_total = 0;
    $ctf_total = 0;
    while($challenge = $stmt->fetch(PDO::FETCH_ASSOC)) {

        echo htmlspecialchars($challenge['title']), ' ', number_format($challenge['points']) ,' / ', number_format($challenge['category_total']), ' (', round(($challenge['points']/$challenge['category_total'])*100), '%)';

        echo '
        <div class="',($challenge['points'] == $challenge['category_total'] ? 'progress progress-success progress-striped' : 'progress progress-striped active'),'">
        <div class="bar" style="width: ',(( $challenge['points']/$challenge['category_total'] ) * 100),'%;"></div>
        </div>
    ';

        $user_total += $challenge['points'];
        $ctf_total += $challenge['category_total'];
    }

    echo 'Total: ', number_format($user_total), ' / ', number_format($ctf_total), ' (', round(($user_total/$ctf_total)*100, 1), '%)';
}

sectionHead('Scoreboard');

echo '
    <table class="table table-striped table-hover">
      <thead>
        <tr>
          <th>#</th>
          <th>Team name</th>
          <th>Points</th>
        </tr>
      </thead>
      <tbody>
     ';

$stmt = $db->query('
    SELECT
    u.id AS user_id,
    u.team_name,
    SUM(c.points) AS score,
    SUM(s.added) AS tiebreaker
    FROM users AS u
    LEFT JOIN submissions AS s ON u.id = s.user_id AND s.correct = 1
    LEFT JOIN challenges AS c ON c.id = s.challenge
    GROUP BY u.id
    ORDER BY score DESC, tiebreaker ASC
');

$i = 1;
while($place = $stmt->fetch(PDO::FETCH_ASSOC)) {

echo '
    <tr>
      <td>', number_format($i) , '</td>
      <td>';
        if ($_SESSION['id']) {

            echo '<a href="user?id=',htmlspecialchars($place['user_id']),'">',
                    ($place['user_id'] == $_SESSION['id'] ? '<span class="label label-info">'.htmlspecialchars($place['team_name']).'</span>' : htmlspecialchars($place['team_name'])),
                 '</a>';
        }
        else {
            echo htmlspecialchars($place['team_name']);
        }
        echo '
      </td>
      <td>' , number_format($place['score']), '</td>
    </tr>
';

    $i++;
}

echo '
      </tbody>
    </table>
     ';

foot();