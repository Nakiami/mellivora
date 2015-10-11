<?php

require('../include/mellivora.inc.php');

login_session_refresh();

send_cache_headers('scores', CONFIG_CACHE_TIME_SCORES);

head(lang_get('scoreboard'));

if (cache_start(CONST_CACHE_NAME_SCORES, CONFIG_CACHE_TIME_SCORES)) {

    $now = time();

    echo '
    <div class="row">
        <div class="col-lg-6">';

    $user_types = db_select_all(
        'user_types',
        array(
            'id',
            'title'
        )
    );

    // no user types
    if (empty($user_types)) {
        section_head(
            lang_get('scoreboard'),
            '<a href="'.CONFIG_SITE_URL.'json?view=scoreboard">
                <img src="'.CONFIG_SITE_URL_STATIC_RESOURCES.'img/json.png" title="View json" alt="json" class="discreet-inline small-icon" />
            </a>',
            false
        );

        $scores = db_query_fetch_all('
            SELECT
               u.id AS user_id,
               u.team_name,
               co.id AS country_id,
               co.country_name,
               co.country_code,
               SUM(c.points) AS score,
               MAX(s.added) AS tiebreaker
            FROM users AS u
            LEFT JOIN countries AS co ON co.id = u.country_id
            LEFT JOIN submissions AS s ON u.id = s.user_id AND s.correct = 1
            LEFT JOIN challenges AS c ON c.id = s.challenge
            WHERE u.competing = 1
            GROUP BY u.id
            ORDER BY score DESC, tiebreaker ASC'
        );

        scoreboard($scores);
    }
    // at least one ser type
    else {
        foreach ($user_types as $user_type) {
            section_head(
                htmlspecialchars($user_type['title']) . ' ' . lang_get('scoreboard'),
                '<a href="'.CONFIG_SITE_URL.'json?view=scoreboard&user_type='.$user_type['id'].'">
                    <img src="'.CONFIG_SITE_URL_STATIC_RESOURCES.'img/json.png" title="View json" alt="json" class="discreet-inline small-icon" />
                 </a>',
                false
            );

            $scores = db_query_fetch_all('
            SELECT
               u.id AS user_id,
               u.team_name,
               co.id AS country_id,
               co.country_name,
               co.country_code,
               SUM(c.points) AS score,
               MAX(s.added) AS tiebreaker
            FROM users AS u
            LEFT JOIN countries AS co ON co.id = u.country_id
            LEFT JOIN submissions AS s ON u.id = s.user_id AND s.correct = 1
            LEFT JOIN challenges AS c ON c.id = s.challenge
            WHERE
              u.competing = 1 AND
              u.user_type = :user_type
            GROUP BY u.id
            ORDER BY score DESC, tiebreaker ASC',
                array(
                    'user_type'=>$user_type['id']
                )
            );

            scoreboard($scores);
        }
    }

    echo '
        </div>  <!-- / span6 -->

        <div class="col-lg-6">
        ';

    section_head(lang_get('challenges'));

    $categories = db_query_fetch_all('
        SELECT
           id,
           title,
           available_from,
           available_until
        FROM
           categories
        WHERE
           available_from < '.$now.' AND
           exposed = 1
        ORDER BY title'
    );

    challenges($categories);

    echo '
        </div> <!-- / span6 -->
    </div> <!-- / row -->
    ';

    cache_end(CONST_CACHE_NAME_SCORES);
}

foot();
