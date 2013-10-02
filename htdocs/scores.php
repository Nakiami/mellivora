<?php

define('IN_FILE', true);
require('../include/general.inc.php');

$now = time();

head('Scoreboard');

echo '
<div class="row-fluid">
    <div class="span6">';

section_head('Scoreboard');
$stmt = $db->query('
    SELECT
    u.id AS user_id,
    u.team_name,
    u.type,
    u.competing,
    SUM(c.points) AS score,
    MAX(s.added) AS tiebreaker
    FROM users AS u
    LEFT JOIN submissions AS s ON u.id = s.user_id AND s.correct = 1
    LEFT JOIN challenges AS c ON c.id = s.challenge
    WHERE u.class = '.CONFIG_UC_USER.'
    GROUP BY u.id
    ORDER BY score DESC, tiebreaker ASC
');
scoreboard($stmt);

section_head('HS Scoreboard');
$stmt = $db->query('
    SELECT
    u.id AS user_id,
    u.team_name,
    u.type,
    u.competing,
    SUM(c.points) AS score,
    MAX(s.added) AS tiebreaker
    FROM users AS u
    LEFT JOIN submissions AS s ON u.id = s.user_id AND s.correct = 1
    LEFT JOIN challenges AS c ON c.id = s.challenge
    WHERE u.class = '.CONFIG_UC_USER.' AND u.type = "hs"
    GROUP BY u.id
    ORDER BY score DESC, tiebreaker ASC
');
scoreboard($stmt);

echo '
    </div>  <!-- / span6 -->

    <div class="span6">
    ';

section_head('Challenges');

$cat_stmt = $db->query('SELECT id, title, available_from, available_until FROM categories ORDER BY title');
while($category = $cat_stmt->fetch(PDO::FETCH_ASSOC)) {

    if ($category['available_from'] && $now < $category['available_from']) {
        continue;
    }

    $chal_stmt = $db->prepare('
        SELECT
        id,
        title,
        points,
        available_from
        FROM challenges
        WHERE category = :category
        ORDER BY points ASC
    ');

    echo '
    <table class="table table-striped table-hover">
      <thead>
        <tr>
          <th>',htmlspecialchars($category['title']),'</th>
          <th>Points</th>
          <th>First solvers</th>
        </tr>
      </thead>
      <tbody>
     ';

    $chal_stmt->execute(array(':category' => $category['id']));
    while($challenge = $chal_stmt->fetch(PDO::FETCH_ASSOC)) {

        if ($challenge['available_from'] && $now < $challenge['available_from']) {
            continue;
        }

        echo '
        <tr>
            <td>';
        if (is_user_logged_in()) {
            echo '<a href="challenge?id=',htmlspecialchars($challenge['id']),'">',htmlspecialchars($challenge['title']),'</a>';
        } else {
            echo htmlspecialchars($challenge['title']);
        }
            echo '</td>
            <td>',number_format($challenge['points']),'</td>

            <td>';

        $pos_stmt = $db->prepare('
            SELECT
            u.team_name,
            s.user_id,
            s.pos
            FROM submissions AS s
            JOIN users AS u ON u.id = s.user_id
            WHERE u.class = '.CONFIG_UC_USER.' AND s.pos >= 1 AND s.pos <= 3 AND s.correct = 1 AND s.challenge=:challenge
            ORDER BY s.pos ASC
        ');
        $pos_stmt->execute(array(':challenge' => $challenge['id']));

        if ($pos_stmt->rowCount()) {
            while($pos = $pos_stmt->fetch(PDO::FETCH_ASSOC)) {

                echo get_position_medal($pos['pos']);

                if (is_user_logged_in()) {
                    echo '<a href="user?id=',htmlspecialchars($pos['user_id']),'">',htmlspecialchars($pos['team_name']), '</a><br />';
                } else {
                    echo htmlspecialchars($pos['team_name']),'<br />';
                }
            }
        }

        else {
            echo '<i>Unsolved</i>';
        }

        echo '
            </td>
        </tr>';
    }
    echo '
    </tbody>
    </table>';
}

echo '
    </div> <!-- / span6 -->
</div> <!-- / row-fluid -->
';

foot();