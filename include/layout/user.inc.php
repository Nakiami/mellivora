<?php

function print_solved_graph($user_id) {
    validate_id($user_id);

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
            'user_id'=>$user_id
        )
    );

    if (empty($challenges)) {
        return;
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
}

function print_solved_challenges($user_id) {
    validate_id($user_id);

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
            'user_id'=>$user_id
        )
    );

    if (count($submissions)) {
        echo '
      <table class="table table-striped table-hover">
        <thead>
          <tr>
            <th>', lang_get('challenge'), '</th>
            <th>', lang_get('solved'), '</th>
            <th>', lang_get('points'), '</th>
          </tr>
        </thead>
        <tbody>
       ';

        foreach ($submissions as $submission) {

            echo '
              <tr>
                <td>
                    <a href="',Config::get('MELLIVORA_CONFIG_SITE_URL'),'challenge?id=', htmlspecialchars($submission['challenge_id']), '">
                    ', htmlspecialchars($submission['title']), '
                    </a> (', htmlspecialchars($submission['category_title']), ')
                </td>

                <td>
                    ', get_position_medal($submission['pos'], true), '
                    ', time_elapsed($submission['added'], $submission['available_from']), ' ', lang_get('after_release'), ' (', date_time($submission['added']), ')
                </td>

                <td>', number_format($submission['points']), '</td>
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
}

function print_user_submissions($user_id, $limit = false) {
    validate_id($user_id);

    section_subhead(
        'Submissions',
        ($limit ? 'Limited to ' . $limit . ' results ': '') . button_link('Show all for user', 'list_submissions?user_id=' . $user_id),
        false
    );

    echo '
    <table id="files" class="table table-striped table-hover">
      <thead>
        <tr>
          <th>Challenge</th>
          <th>Added</th>
          <th>Flag</th>
          <th>Correct</th>
          <th>Manage</th>
        </tr>
      </thead>
      <tbody>
    ';

    $submissions = db_query_fetch_all('
        SELECT
           s.id,
           u.id AS user_id,
           u.team_name,
           s.added,
           s.correct,
           s.flag,
           c.id AS challenge_id,
           c.title AS challenge_title
        FROM submissions AS s
        LEFT JOIN users AS u on s.user_id = u.id
        LEFT JOIN challenges AS c ON c.id = s.challenge
        WHERE user_id = :user_id
        ORDER BY s.added DESC
        LIMIT '.$limit,
        array(
            'user_id' => $user_id
        )
    );

    foreach($submissions as $submission) {
        echo '
    <tr>
        <td><a href="',Config::get('MELLIVORA_CONFIG_SITE_URL'),'challenge.php?id=',htmlspecialchars($submission['challenge_id']),'">',htmlspecialchars($submission['challenge_title']),'</a></td>
        <td>',time_elapsed($submission['added']),' ago</td>
        <td>',htmlspecialchars($submission['flag']),'</td>
        <td>
            ',($submission['correct'] ?
            '<img src="'.Config::get('MELLIVORA_CONFIG_SITE_URL_STATIC_RESOURCES').'img/accept.png" alt="Correct!" title="Correct!" />' :
            '<img src="'.Config::get('MELLIVORA_CONFIG_SITE_URL_STATIC_RESOURCES').'img/stop.png" alt="Wrong!" title="Wrong!" />'),'
        </td>
        <td>
            <form method="post" action="actions/list_submissions" class="discreet-inline">';
        form_xsrf_token();
        echo '
                <input type="hidden" name="action" value="',($submission['correct'] ? 'mark_incorrect' : 'mark_correct'),'" />
                <input type="hidden" name="id" value="',htmlspecialchars($submission['id']),'" />
                <button type="submit" class="btn btn-sm btn-',($submission['correct'] ? 'warning' : 'success'),'">Mark ',($submission['correct'] ? 'incorrect' : 'correct'),'</button>
            </form>

            <form method="post" action="actions/list_submissions" class="discreet-inline">';
        form_xsrf_token();
        echo '
                <input type="hidden" name="action" value="delete" />
                <input type="hidden" name="id" value="',htmlspecialchars($submission['id']),'" />
                <button type="submit" class="btn btn-sm btn-danger">Delete</button>
            </form>
        </td>
    </tr>
    ';
    }

    echo '
      </tbody>
    </table>
     ';
}

function print_user_exception_log($user_id, $limit = false) {
    validate_id($user_id);

    section_subhead(
        'Exception log',
        ($limit ? 'Limited to ' . $limit . ' results ': '') . button_link('Show all for user', 'list_exceptions?user_id=' . $user_id),
        false
    );

    $exceptions = db_query_fetch_all('
        SELECT
           e.id,
           e.message,
           e.added,
           e.added_by,
           e.trace,
           INET_NTOA(e.user_ip) AS user_ip,
           u.team_name
        FROM exceptions AS e
        LEFT JOIN users AS u ON u.id = e.added_by
        WHERE e.added_by = :user_id
        ORDER BY e.id DESC
        '.($limit ? 'LIMIT '.$limit : ''),
        array(
            'user_id'=>$user_id
        )
    );

    if (count($exceptions)) {

        echo '
    <table id="hints" class="table table-striped table-hover">
      <thead>
        <tr>
          <th>Message</th>
          <th>Added</th>
          <th>IP</th>
          <th>Trace</th>
        </tr>
      </thead>
      <tbody>
    ';

        foreach ($exceptions as $exception) {
            echo '
    <tr>
        <td>', htmlspecialchars($exception['message']), '</td>
        <td>', date_time($exception['added']), '</td>
        <td><a href="', Config::get('MELLIVORA_CONFIG_SITE_ADMIN_URL'), 'list_ip_log.php?ip=', htmlspecialchars($exception['user_ip']), '">', htmlspecialchars($exception['user_ip']), '</a></td>
        <td>', htmlspecialchars($exception['trace']), '</td>
    </tr>
    ';
        }

        echo '
      </tbody>
    </table>
     ';
    }

    else {
        message_inline_blue(lang_get('no_exceptions'));
    }
}

function print_user_ip_log($user_id, $limit = 0) {

    validate_id($user_id);

    section_subhead(
        'IP address usage',
        ($limit ? 'Limited to ' . $limit . ' results ': '') . button_link('Show all for user', 'list_ip_log?user_id=' . htmlspecialchars($user_id)),
        false
    );

    echo '
        <table id="files" class="table table-striped table-hover">
          <thead>
            <tr>
              <th>IP</th>
              <th>Hostname</th>
              <th>First used</th>
              <th>Last used</th>
              <th>Times used</th>
            </tr>
          </thead>
          <tbody>
        ';



    $entries = db_query_fetch_all('
        SELECT
            INET_NTOA(ip) AS ip,
            added,
            last_used,
            times_used
        FROM ip_log
        WHERE user_id = :user_id
        ORDER BY last_used DESC
        '.($limit ? 'LIMIT '.$limit : ''),
        array(
            'user_id'=>$user_id
        )
    );

    foreach($entries as $entry) {
        echo '
        <tr>
            <td><a href="',Config::get('MELLIVORA_CONFIG_SITE_ADMIN_URL'),'list_ip_log.php?ip=',htmlspecialchars($entry['ip']),'">',htmlspecialchars($entry['ip']),'</a></td>
            <td>',(Config::get('MELLIVORA_CONFIG_GET_IP_HOST_BY_ADDRESS') ? htmlspecialchars(gethostbyaddr($entry['ip'])) : '<i>Lookup disabled in config</i>'),'</td>
            <td>',date_time($entry['added']),'</td>
            <td>',date_time($entry['last_used']),'</td>
            <td>',number_format($entry['times_used']),'</td>
        </tr>
        ';
    }

    echo '
          </tbody>
        </table>
         ';
}