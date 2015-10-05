<?php

require('../include/mellivora.inc.php');

validate_id(array_get($_GET, 'id'));

head(lang_get('user_details'));

if (cache_start(CONST_CACHE_NAME_USER . $_GET['id'], CONFIG_CACHE_TIME_USER)) {

    $user = db_query_fetch_one('
        SELECT
            u.team_name,
            u.competing,
            co.country_name,
            co.country_code
        FROM users AS u
        LEFT JOIN countries AS co ON co.id = u.country_id
        WHERE
          u.id = :user_id',
        array('user_id' => $_GET['id'])
    );

    if (empty($user)) {
        message_generic(
            lang_get('sorry'),
            lang_get('no_user_found'),
            false);
    }

    section_head(htmlspecialchars($user['team_name']), country_flag_link($user['country_name'], $user['country_code'], true), false);

    if (!$user['competing']) {
        message_inline_blue(lang_get('non_competing_user'));
    }

    $challenges = db_query_fetch_all('
        SELECT
           ca.title,
           (SELECT SUM(ch.points) FROM challenges AS ch JOIN submissions AS s ON s.challenge = ch.id AND s.user_id = :user_id AND s.correct = 1 WHERE ch.category = ca.id GROUP BY ch.category) AS points,
           (SELECT SUM(ch.points) FROM challenges AS ch WHERE ch.category = ca.id GROUP BY ch.category) AS category_total
        FROM categories AS ca
        WHERE
          ca.available_from < UNIX_TIMESTAMP() AND
          ca.exposed = 1
        ORDER BY ca.title ASC',
        array(
            'user_id'=>$_GET['id']
        )
    );

    if (empty($challenges)) {
        message_generic(
            lang_get('no_information'),
            lang_get('no_solves'),
            false
        );
    }

    $user_total = 0;
    $ctf_total = 0;
    foreach($challenges as $challenge) {
      echo '<strong>',htmlspecialchars($challenge['title']), '</strong>, ', number_format($challenge['points']) ,' / ', number_format($challenge['category_total']), ' (', round(($challenge['points']/max(1, $challenge['category_total']))*100), '%)';

      progress_bar(($challenge['points']/max(1, $challenge['category_total'])) * 100);

      $user_total += $challenge['points'];
      $ctf_total += $challenge['category_total'];
    }

    echo lang_get('total_solves'), ' ', number_format($user_total), ' / ', number_format($ctf_total), ' (', round(($user_total/$ctf_total)*100, 1), '%)';

    section_head(lang_get('solved_challenges'));

    $submissions = db_query_fetch_all('
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
           s.user_id = :user_id AND
           ch.exposed = 1 AND
           ca.exposed = 1
        ORDER BY s.added DESC',
        array(
            'user_id'=>$_GET['id']
        )
    );

    if (count($submissions)) {
      echo '
      <table class="table table-striped table-hover">
        <thead>
          <tr>
            <th>',lang_get('challenge'),'</th>
            <th>',lang_get('solved'),'</th>
            <th>',lang_get('points'),'</th>
          </tr>
        </thead>
        <tbody>
       ';

      foreach ($submissions as $submission) {

          echo '
              <tr>
                <td>
                    <a href="challenge?id=',htmlspecialchars($submission['challenge_id']),'">
                    ',htmlspecialchars($submission['title']),'
                    </a> (',htmlspecialchars($submission['category_title']),')
                </td>

                <td>
                    ',get_position_medal($submission['pos'], true),'
                    ',time_elapsed($submission['added'], $submission['available_from']),' ',lang_get('after_release'),' (',date_time($submission['added']),')
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
        message_inline_blue(lang_get('no_challenges_solved'));
    }

    cache_end(CONST_CACHE_NAME_USER . $_GET['id']);
}

foot();