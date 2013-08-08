<?php

define('IN_FILE', true);
require('../include/general.inc.php');

head('Scoreboard');

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