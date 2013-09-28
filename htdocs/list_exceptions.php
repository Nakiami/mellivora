<?php

define('IN_FILE', true);
require('../include/general.inc.php');

enforceAuthentication(CONFIG_UC_MODERATOR);

head('Exceptions');
managementMenu();
sectionHead('Exceptions');

echo '
    <table id="hints" class="table table-striped table-hover">
      <thead>
        <tr>
          <th>Message</th>
          <th>Added</th>
          <th>Added by</th>
          <th>IP</th>
          <th>Trace</th>
          <th>User agent</th>
        </tr>
      </thead>
      <tbody>
    ';

$stmt = $db->query('
    SELECT
    e.id,
    e.message,
    e.added,
    e.added_by,
    e.trace,
    INET_NTOA(e.user_ip) AS user_ip,
    e.user_agent,
    u.team_name
    FROM exceptions AS e
    LEFT JOIN users AS u ON u.id = e.added_by
    ORDER BY e.id DESC
');
while($exception = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo '
    <tr>
        <td>',htmlspecialchars($exception['message']),'</td>
        <td>',getDateTime($exception['added']),'</td>
        <td>',($exception['added_by'] ?
         '<a href="user.php?id='.htmlspecialchars($exception['added_by']).'">'.htmlspecialchars($exception['team_name']).'</a>'
         :
         '<i>Not logged in</i>'),'
        </td>
        <td><a href="list_ip_log.php?ip=',htmlspecialchars($exception['user_ip']),'">',htmlspecialchars($exception['user_ip']),'</a></td>
        <td>',htmlspecialchars($exception['trace']),'</td>
        <td>',htmlspecialchars($exception['user_agent']),'</td>
    </tr>
    ';
}

echo '
      </tbody>
    </table>
     ';

foot();