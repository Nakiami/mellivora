<?php

require('../../include/mellivora.inc.php');

enforce_authentication(CONST_USER_CLASS_MODERATOR);

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
          <th>Last active</th>
          <th class="center">Class</th>
          <th class="center">Enabled</th>
          <th class="center">Num IPs</th>
          <th class="center">Manage</th>
        </tr>
      </thead>
      <tbody>
    ';

$values = array();
$search_for = array_get($_GET, 'search_for');
if ($search_for) {
    $values['search_for_team_name'] = '%'.$search_for.'%';
    $values['search_for_email'] = '%'.$search_for.'%';

    $res = db_query('
        SELECT COUNT(*) AS num
        FROM users AS u
        WHERE u.team_name LIKE :search_for_team_name OR u.email LIKE :search_for_email
    ', $values, false);

    $total_results = $res['num'];
}
// no search
else {
    $total_results = db_count_num('users');
}

$from = get_pager_from($_GET);
$results_per_page = 100;

$users = db_query_fetch_all('
    SELECT
       u.id,
       u.email,
       u.team_name,
       u.added,
       u.last_active,
       u.class,
       u.enabled,
       co.country_name,
       co.country_code,
       COUNT(ipl.id) AS num_ips
    FROM users AS u
    LEFT JOIN ip_log AS ipl ON ipl.user_id = u.id
    LEFT JOIN countries AS co ON co.id = u.country_id
    '.($search_for ? 'WHERE u.team_name LIKE :search_for_team_name OR u.email LIKE :search_for_email' : '').'
    GROUP BY u.id
    ORDER BY u.team_name ASC
    LIMIT '.$from.', '.$results_per_page,
    $values
);

$total_results = isset($total_results) ? $total_results : count($users);

$base_url = CONFIG_SITE_ADMIN_URL . 'list_users';

pager($base_url, $total_results, $results_per_page, $from);

foreach($users as $user) {
    echo '
    <tr>
        <td>
            ',country_flag_link($user['country_name'], $user['country_code']),'
            <a href="',CONFIG_SITE_ADMIN_URL,'user?id=',htmlspecialchars($user['id']),'">',htmlspecialchars($user['team_name']),'</a>
        </td>
        <td><a href="',CONFIG_SITE_ADMIN_URL,'new_email.php?to=',htmlspecialchars($user['email']),'">',htmlspecialchars($user['email']),'</a></td>
        <td>',date_time($user['added']),'</td>
        <td>',($user['last_active'] ? date_time($user['last_active']) : '<i>Never</i>'),'</td>
        <td class="center">',user_class_name($user['class']),'</td>
        <td class="center">',($user['enabled'] ? '<span class="glyphicon glyphicon-ok"></span>' : '<span class="glyphicon glyphicon-remove red"></span>'),'</td>
        <td class="center"><a href="',CONFIG_SITE_ADMIN_URL,'list_ip_log.php?user_id=',htmlspecialchars($user['id']),'">',number_format($user['num_ips']), '</a></td>
        <td class="center">
            <a href="',CONFIG_SITE_ADMIN_URL,'edit_user.php?id=',htmlspecialchars($user['id']),'" class="btn btn-xs btn-primary">Edit</a>
        </td>
    </tr>
    ';
}

echo '
      </tbody>
    </table>
     ';

foot();