<?php

require('../include/mellivora.inc.php');

login_session_refresh();

head('Scoreboard');

if (cache_start('scores', CONFIG_CACHE_TIME_SCORES)) {

    $now = time();

    echo '
    <div class="row">
        <div class="col-lg-6">';

    section_head('Scoreboard');
    $scores = db_query_fetch_all('
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
        WHERE u.competing = 1
        GROUP BY u.id
        ORDER BY score DESC, tiebreaker ASC'
    );
    scoreboard($scores);

    section_head('HS Scoreboard');
    $scores = db_query_fetch_all('
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
        WHERE u.competing = 1 AND u.type = :type
        GROUP BY u.id
        ORDER BY score DESC, tiebreaker ASC',
        array(
            'type'=>'hs'
        )
    );
    scoreboard($scores);

    echo '
        </div>  <!-- / span6 -->

        <div class="col-lg-6">
        ';

    section_head('Challenges');

    $categories = db_query_fetch_all('
        SELECT
           id,
           title,
           available_from,
           available_until
        FROM
           categories
        WHERE
           available_from < '.$now.'
        ORDER BY title'
    );

    foreach($categories as $category) {

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

        $challenges = db_query_fetch_all('
            SELECT
               id,
               title,
               points,
               available_from
            FROM challenges
            WHERE
              available_from < '.$now.' AND category=:category
            ORDER BY points ASC',
            array(
                'category'=>$category['id']
            )
        );

        foreach($challenges as $challenge) {

            echo '
            <tr>
                <td>
                    <a href="challenge?id=',htmlspecialchars($challenge['id']),'">',htmlspecialchars($challenge['title']),'</a>
                </td>

                <td>
                    ',number_format($challenge['points']),'
                </td>

                <td>';

            $users = db_query_fetch_all('
                SELECT
                   u.id,
                   u.team_name
                FROM users AS u
                JOIN submissions AS s ON s.user_id = u.id
                WHERE
                   u.competing = 1 AND
                   s.correct = 1 AND
                   s.challenge=:challenge
                ORDER BY s.added ASC
                LIMIT 3',
                array(
                    'challenge'=>$challenge['id']
                )
            );

            if (count($users)) {
                $pos = 1;
                foreach($users as $user) {
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
    </div> <!-- / row -->
    ';

    cache_end('scores');
}

foot();