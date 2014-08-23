<?php

require('../../include/mellivora.inc.php');

enforce_authentication(CONFIG_UC_MODERATOR);

head('Submissions');
menu_management();

if (!isset($_GET['all'])) {
    $_GET['all'] = 0;
}

if ($_GET['all']) {
    section_head('All submissions', '<a href="list_submissions?all=0">Show only submissions in need of marking</a>', false);
} else {
    section_head('Submissions in need of marking', '<a href="list_submissions?all=1">List all submissions</a>', false);
}

$num_subs = db_query_fetch_one('
    SELECT
       COUNT(*) AS num
    FROM submissions AS s
    LEFT JOIN challenges AS c ON c.id = s.challenge
    '.($_GET['all'] ? '' : 'WHERE c.automark = 0 AND s.marked = 0').'
');

$from = get_pager_from($_GET);
$results_per_page = 70;

pager(CONFIG_SITE_ADMIN_URL.'list_submissions/?'.(isset($_GET['all']) ? 'all='.$_GET['all'] : ''), $num_subs['num'], $results_per_page, $from);

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
    FROM submissions AS s
    LEFT JOIN users AS u on s.user_id = u.id
    LEFT JOIN challenges AS c ON c.id = s.challenge
    '.($_GET['all'] ? '' : 'WHERE c.automark = 0 AND s.marked = 0').'
    ORDER BY s.added DESC
    LIMIT '.$from.', '.$results_per_page);

foreach($submissions as $submission) {
    echo '
    <tr>
        <td><a href="../challenge.php?id=',htmlspecialchars($submission['challenge_id']),'">',htmlspecialchars($submission['challenge_title']),'</a></td>
        <td><a href="/user.php?id=',htmlspecialchars($submission['user_id']),'">',htmlspecialchars($submission['team_name']),'</a></td>
        <td>',time_elapsed($submission['added']),' ago</td>
        <td>',htmlspecialchars($submission['flag']),'</td>
        <td>
            ',($submission['correct'] ?
                '<img src="'.CONFIG_SITE_URL.'img/accept.png" alt="Correct!" title="Correct!" />' :
                '<img src="'.CONFIG_SITE_URL.'img/stop.png" alt="Wrong!" title="Wrong!" />'),'
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
