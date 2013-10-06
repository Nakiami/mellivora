<?php

define('IN_FILE', true);
require('../include/general.inc.php');

$cache = new Cache_Lite_Output(array('cacheDir'=>CONFIG_CACHE_PATH, 'lifeTime'=>CONFIG_CACHE_TIME_SCORES));
if (!($cache->start('scores'))) {

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

    $cat_stmt = $db->query('
      SELECT
        id,
        title,
        available_from,
        available_until
      FROM
        categories
      WHERE
        available_from < '.$now.'
      ORDER BY title
      ');
    while($category = $cat_stmt->fetch(PDO::FETCH_ASSOC)) {

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

        $chal_stmt = $db->prepare('
            SELECT
              id,
              title,
              points,
              available_from
            FROM challenges
            WHERE
              available_from < '.$now.' AND category=:category
            ORDER BY points ASC
        ');

        $chal_stmt->execute(array(':category' => $category['id']));
        while($challenge = $chal_stmt->fetch(PDO::FETCH_ASSOC)) {

            echo '
            <tr>
                <td>
                    <a href="challenge?id=',htmlspecialchars($challenge['id']),'">',htmlspecialchars($challenge['title']),'</a>
                </td>

                <td>
                    ',number_format($challenge['points']),'
                </td>

                <td>';

            $pos_stmt = $db->prepare('
                SELECT
                  u.id,
                  u.team_name
                FROM users AS u
                JOIN submissions AS s ON s.user_id = u.id
                WHERE s.correct = 1 AND s.challenge=:challenge
                ORDER BY s.added ASC
                LIMIT 3
            ');
            $pos_stmt->execute(array(':challenge' => $challenge['id']));

            if ($pos_stmt->rowCount()) {
                $pos = 1;
                while($user = $pos_stmt->fetch(PDO::FETCH_ASSOC)) {
                    echo get_position_medal($pos++),
                    '<a href="user?id=',htmlspecialchars($user['id']),'">',htmlspecialchars($user['team_name']), '</a><br />';
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

    $cache->end();
}