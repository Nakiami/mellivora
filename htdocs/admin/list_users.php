<?php

require('../../include/mellivora.inc.php');

enforce_authentication(CONFIG_UC_MODERATOR);

head('Users');
menu_management();
section_head('Users');

echo '
    <table id="files" class="table table-striped table-hover">
      <thead>
        <tr>
          <th>Team</th>
          <th>Email</th>
          <th>Added</th>
          <th>Class</th>
          <th>Enabled</th>
          <th>Num IPs</th>
          <th>Manage</th>
        </tr>
      </thead>
      <tbody>
    ';

$from = get_pager_from($_GET);
$num_users = db_count_num('users');
$results_per_page = 100;

pager(CONFIG_SITE_ADMIN_URL.'list_users/', $num_users, $results_per_page, $from);

$users = db_query_fetch_all('
    SELECT
       u.id,
       u.email,
       u.team_name,
       u.added,
       u.class,
       u.enabled,
       co.country_name,
       co.country_code,
       COUNT(ipl.id) AS num_ips
    FROM users AS u
    LEFT JOIN ip_log AS ipl ON ipl.user_id = u.id
    LEFT JOIN countries AS co ON co.id = u.country_id
    GROUP BY u.id
    ORDER BY u.team_name ASC
    LIMIT '.$from.', '.$results_per_page);

foreach($users as $user) {
    echo '
    <tr>
        <td>
            ',country_flag_link($user['country_name'], $user['country_code']),'
            <a href="',CONFIG_SITE_URL,'user?id=',htmlspecialchars($user['id']),'">',htmlspecialchars($user['team_name']),'</a>
        </td>
        <td>',htmlspecialchars($user['email']),'</td>
        <td>',date_time($user['added']),'</td>
        <td>',user_class_name($user['class']),'</td>
        <td>',($user['enabled'] ? 'Yes' : 'No'),'</td>
        <td><a href="list_ip_log.php?id=',htmlspecialchars($user['id']),'">',number_format($user['num_ips']), '</a></td>
        <td>
            <a href="edit_user.php?id=',htmlspecialchars($user['id']),'" class="btn btn-xs btn-primary">Edit</a>
        </td>
    </tr>
    ';
}

echo '
      </tbody>
    </table>
     ';

foot();