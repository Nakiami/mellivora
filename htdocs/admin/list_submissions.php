<?php

require('../../include/mellivora.inc.php');

enforce_authentication(CONST_USER_CLASS_MODERATOR);

head('Submissions');
menu_management();

$where = array();
if (array_get($_GET, 'only_needing_marking')) {
    $only_needing_marking = true;
    $where['automark'] = 0;
    $where['marked'] = 0;
} else {
    $only_needing_marking = false;
}

if (is_valid_id(array_get($_GET, 'user_id'))) {
    $where['user_id'] = $_GET['user_id'];
}

$query = '
    FROM submissions AS s
    LEFT JOIN users AS u on s.user_id = u.id
    LEFT JOIN challenges AS c ON c.id = s.challenge
';

if (!empty($where)) {
    $query .= 'WHERE '.implode('=? AND ', array_keys($where)).'=? ';
}

if (array_get($_GET, 'user_id')) {
    section_head('User submissions', button_link('List all submissions', 'list_submissions?only_needing_marking=0'), false);
} else if ($only_needing_marking) {
    section_head('Submissions in need of marking', button_link('List all submissions', 'list_submissions?only_needing_marking=0'), false);
} else {
    section_head('All submissions', button_link('Show only submissions in need of marking', 'list_submissions?only_needing_marking=1'), false);
}

$num_subs = db_query_fetch_one('
    SELECT
       COUNT(*) AS num
    '. $query,
    array_values($where)
);

$from = get_pager_from($_GET);
$results_per_page = 70;

pager(CONFIG_SITE_ADMIN_URL.'list_submissions', $num_subs['num'], $results_per_page, $from);

echo '
    <table id="files" class="table table-striped table-hover">
      <thead>
        <tr>
          <th>Challenge</th>
          <th>Team name</th>
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
    '.$query.'
    ORDER BY s.added DESC
    LIMIT '.$from.', '.$results_per_page,
    array_values($where)
);

foreach($submissions as $submission) {
    echo '
    <tr>
        <td><a href="',CONFIG_SITE_URL,'challenge.php?id=',htmlspecialchars($submission['challenge_id']),'">',htmlspecialchars($submission['challenge_title']),'</a></td>
        <td><a href="',CONFIG_SITE_ADMIN_URL,'user.php?id=',htmlspecialchars($submission['user_id']),'">',htmlspecialchars($submission['team_name']),'</a></td>
        <td>',time_elapsed($submission['added']),' ago</td>
        <td>',htmlspecialchars($submission['flag']),'</td>
        <td>
            ',($submission['correct'] ?
                '<img src="'.CONFIG_SITE_URL_STATIC_RESOURCES.'img/accept.png" alt="Correct!" title="Correct!" />' :
                '<img src="'.CONFIG_SITE_URL_STATIC_RESOURCES.'img/stop.png" alt="Wrong!" title="Wrong!" />'),'
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

foot();
