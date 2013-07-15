<?php

define('IN_FILE', true);
require('../include/general.inc.php');

head('Scoreboard');

$cat_stmt = $db->query('
    SELECT
    u.team_name,
    SUM(c.points) AS score
    FROM users AS u
    LEFT JOIN submissions AS s ON u.id = s.user AND s.correct = 1
    LEFT JOIN challenges AS c ON c.id = s.challenge
    GROUP BY u.id
    ORDER BY score DESC
');

if ($_SESSION['id']) {

    echo '<div class="page-header"><h2>Your progress</h2></div>';

    $stmt = $db->prepare('
        SELECT
        ca.title,
        (SELECT SUM(ch.points) FROM challenges AS ch JOIN submissions AS s ON s.challenge = ch.id AND s.user = :user AND s.correct = 1 WHERE ch.category = ca.id GROUP BY ch.category) AS points,
        (SELECT SUM(ch.points) FROM challenges AS ch WHERE ch.category = ca.id GROUP BY ch.category) AS category_total
        FROM categories AS ca
        ');
    $stmt->execute(array(':user' => $_SESSION['id']));

    $user_total = 0;
    $ctf_total = 0;
    while($challenge = $stmt->fetch(PDO::FETCH_ASSOC)) {

        echo htmlspecialchars($challenge['title']), ' ', number_format($challenge['points']) ,'/', number_format($challenge['category_total']);

        echo '
        <div class="',($challenge['points'] == $challenge['category_total'] ? 'progress progress-success progress-striped' : 'progress progress-striped active'),'">
        <div class="bar" style="width: ',(( $challenge['points']/$challenge['category_total'] ) * 100),'%;"></div>
        </div>
    ';

        $user_total += $challenge['points'];
        $ctf_total += $challenge['category_total'];
    }

    echo 'Total: ', $user_total, ' / ', $ctf_total, ' (', round(($user_total/$ctf_total)*100), '%)';
}

echo '<div class="page-header"><h2>Scoreboard</h2></div>';

echo '
    <table class="table table-striped">
      <thead>
        <tr>
          <th>#</th>
          <th>Team name</th>
          <th>Points</th>
        </tr>
      </thead>
      <tbody>
     ';

$i = 1;
while($place = $cat_stmt->fetch(PDO::FETCH_ASSOC)) {

echo '
    <tr>
      <td>',$i,'</td>
      <td>', htmlspecialchars($place['team_name']) , '</td>
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