<?php

define('IN_FILE', true);
require('../include/general.inc.php');
enforce_authentication();

validate_id($_GET['id']);

head('User details');

$stmt = $db->prepare('SELECT team_name FROM users WHERE id=:user_id');
$stmt->execute(array('user_id'=>$_GET['id']));
$submission = $stmt->fetch(PDO::FETCH_ASSOC);

section_head($submission['team_name']);

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

  echo '<strong>',htmlspecialchars($challenge['title']), '</strong>, ', number_format($challenge['points']) ,' / ', number_format($challenge['category_total']), ' (', round(($challenge['points']/$challenge['category_total'])*100), '%)';

  echo '
  <div class="',($challenge['points'] == $challenge['category_total'] ? 'progress progress-success progress-striped' : 'progress progress-striped'),'">
  <div class="bar" style="width: ',(( $challenge['points']/$challenge['category_total'] ) * 100),'%;"></div>
  </div>
';

  $user_total += $challenge['points'];
  $ctf_total += $challenge['category_total'];
}

echo 'Total: ', number_format($user_total), ' / ', number_format($ctf_total), ' (', round(($user_total/$ctf_total)*100, 1), '%)';

section_head('Solved challenges');

$stmt = $db->prepare('
    SELECT
    s.added,
    ((SELECT COUNT(*) FROM submissions AS ss WHERE ss.correct = 1 AND ss.added < s.added AND ss.challenge=s.challenge)+1) AS pos,
    ch.id AS challenge_id,
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

if ($stmt->rowCount()) {
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
            <td>
                <a href="challenge?id=',htmlspecialchars($submission['challenge_id']),'">
                ',htmlspecialchars($submission['title']),'
                </a> (',htmlspecialchars($submission['category_title']),')
            </td>

            <td>
                ',get_position_medal($submission['pos']),'
                ',get_time_elapsed($submission['added'], $submission['available_from']),' after release, ',get_time_elapsed($submission['added']),' ago (',get_date_time($submission['added']),')
            </td>

            <td>',number_format($submission['points']),'</td>
          </tr>
          ';
  }

  echo '
    </tbody>
  </table>
      ';
}

else {
  echo '
  <div class="alert alert-info">
      No challenges solved, yet!
  </div>
  ';
}

foot();