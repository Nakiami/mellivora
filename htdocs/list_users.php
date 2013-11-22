<?php

require('../include/mellivora.inc.php');

enforce_authentication(CONFIG_UC_MODERATOR);

head('Users');
menu_management();
section_head('Users');

echo '
    <table id="files" class="table table-striped table-hover">
      <thead>
        <tr>
          <th>Team name</th>
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

$stmt = $db->query('
    SELECT
    u.id,
    u.email,
    u.team_name,
    u.added,
    u.class,
    u.enabled,
    COUNT(ipl.id) AS num_ips
    FROM users AS u
    LEFT JOIN ip_log AS ipl ON ipl.user_id = u.id
    GROUP BY u.id
    ORDER BY u.team_name
    ');
while($user = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo '
    <tr>
        <td>',htmlspecialchars($user['team_name']),'</td>
        <td>',htmlspecialchars($user['email']),'</td>
        <td>',date_time($user['added']),'</td>
        <td>',user_class_name($user['class']),'</td>
        <td>',($user['enabled'] ? 'Yes' : 'No'),'</td>
        <td><a href="list_ip_log.php?id=',htmlspecialchars($user['id']),'">',number_format($user['num_ips']),'</a></td>
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