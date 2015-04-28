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
          <th>Class</th>
          <th>Enabled</th>
          <th>Num IPs</th>
          <th>Manage</th>
        </tr>
      </thead>
      <tbody>
    ';

$values = array();
$search_for = array_get($_GET, 'search_for');
if ($search_for) {
    $values['search_for_team_name'] = '%'.$search_for.'%';
    $values['search_for_email'] = '%'.$search_for.'%';
} else {
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

$base_url = CONFIG_SITE_ADMIN_URL . 'list_users/' . (isset($_GET['search_for']) ? '?search_for=' . $_GET['search_for'] : '');

pager($base_url, $total_results, $results_per_page, $from);

foreach($users as $user) {
    echo '
    <tr>
        <td>
            ',country_flag_link($user['country_name'], $user['country_code']),'
            <a href="',CONFIG_SITE_URL,'user?id=',htmlspecialchars($user['id']),'">',htmlspecialchars($user['team_name']),'</a>
        </td>
        <td><a href="',CONFIG_SITE_ADMIN_URL,'new_email.php?to=',htmlspecialchars($user['email']),'">',htmlspecialchars($user['email']),'</a></td>
        <td>',date_time($user['added']),'</td>
        <td>',user_class_name($user['class']),'</td>
        <td>',($user['enabled'] ? 'Yes' : 'No'),'</td>
        <td><a href="',CONFIG_SITE_ADMIN_URL,'list_ip_log.php?id=',htmlspecialchars($user['id']),'">',number_format($user['num_ips']), '</a></td>
        <td>
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