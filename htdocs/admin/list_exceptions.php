<?php

require('../../include/mellivora.inc.php');

enforce_authentication(CONST_USER_CLASS_MODERATOR);

head('Exceptions');
menu_management();

if (array_get($_GET, 'user_id')) {
    section_subhead('User exceptions', button_link('Show all exceptions', 'list_exceptions'), false);
} else {
    section_subhead('Exceptions', button_link('Clear exceptions', 'edit_exceptions'), false);
}

echo '
    <table id="hints" class="table table-striped table-hover">
      <thead>
        <tr>
          <th>Message</th>
          <th>Added</th>
          <th>User</th>
          <th>IP</th>
        </tr>
      </thead>
      <tbody>
    ';

$where = array();
if (is_valid_id(array_get($_GET, 'user_id'))) {
    $where['added_by'] = $_GET['user_id'];
}

$from = get_pager_from($_GET);
$num_exceptions = db_count_num('exceptions', $where);

pager(
    CONFIG_SITE_ADMIN_URL.'list_exceptions',
    $num_exceptions,
    CONST_NUM_EXCEPTIONS_PER_PAGE,
    $from
);

$query = 'SELECT
       e.id,
       e.message,
       e.added,
       e.added_by,
       e.trace,
       INET_NTOA(e.user_ip) AS user_ip,
       u.team_name
    FROM exceptions AS e
    LEFT JOIN users AS u ON u.id = e.added_by
    ';

if (!empty($where)) {
    $query .= 'WHERE '.implode('=? AND ', array_keys($where)).'=? ';
}

$query .= 'ORDER BY e.id DESC
           LIMIT '.$from.', '.CONST_NUM_EXCEPTIONS_PER_PAGE;

$exceptions = db_query_fetch_all($query, array_values($where));

foreach($exceptions as $exception) {
    echo '
    <tr>
        <td>',htmlspecialchars($exception['message']),'</td>
        <td>',date_time($exception['added']),'</td>
        <td>',($exception['added_by'] ?
         '<a href="'.CONFIG_SITE_ADMIN_URL.'user.php?id='.htmlspecialchars($exception['added_by']).'">'.htmlspecialchars($exception['team_name']).'</a>'
         :
         '<i>N/A</i>'),'
        </td>
        <td><a href="',CONFIG_SITE_ADMIN_URL,'list_ip_log.php?ip=',htmlspecialchars($exception['user_ip']),'">',htmlspecialchars($exception['user_ip']),'</a></td>
    </tr>
    <tr>
        <td colspan="4">
            <pre>',nl2br(htmlspecialchars($exception['trace'])),' </pre>
        </td>
    </tr>
    ';
}

echo '
      </tbody>
    </table>
     ';

foot();
