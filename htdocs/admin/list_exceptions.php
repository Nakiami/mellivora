<?php

require('../../include/mellivora.inc.php');

enforce_authentication(CONST_USER_CLASS_MODERATOR);

head('Exceptions');
menu_management();

section_subhead('Exceptions', '| <a href="edit_exceptions">Clear exceptions</a>', false);

echo '
    <table id="hints" class="table table-striped table-hover">
      <thead>
        <tr>
          <th>Message</th>
          <th>Added</th>
          <th>User</th>
          <th>IP</th>
          <th>Trace</th>
        </tr>
      </thead>
      <tbody>
    ';

$from = get_pager_from($_GET);
$num_exceptions = db_count_num('exceptions');
$results_per_page = 30;

pager(CONFIG_SITE_ADMIN_URL.'list_exceptions/', $num_exceptions, $results_per_page, $from);

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
    ORDER BY e.id DESC
    LIMIT '.$from.', '.$results_per_page);

foreach($exceptions as $exception) {
    echo '
    <tr>
        <td>',htmlspecialchars($exception['message']),'</td>
        <td>',date_time($exception['added']),'</td>
        <td>',($exception['added_by'] ?
         '<a href="'.CONFIG_SITE_ADMIN_URL.'edit_user.php?id='.htmlspecialchars($exception['added_by']).'">'.htmlspecialchars($exception['team_name']).'</a>'
         :
         '<i>N/A</i>'),'
        </td>
        <td><a href="',CONFIG_SITE_ADMIN_URL,'list_ip_log.php?ip=',htmlspecialchars($exception['user_ip']),'">',htmlspecialchars($exception['user_ip']),'</a></td>
        <td>',htmlspecialchars($exception['trace']),'</td>
    </tr>
    ';
}

echo '
      </tbody>
    </table>
     ';

foot();
