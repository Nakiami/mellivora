<?php

require('../../include/mellivora.inc.php');

enforce_authentication(CONST_USER_CLASS_MODERATOR);

head('IP log');
menu_management();

$where = array();

if (is_valid_ip(array_get($_GET, 'ip'))) {
    section_head('Teams using IP ' . $_GET['ip']);
    $where['ip'] = ip2long($_GET['ip']);
} else if (is_valid_id(array_get($_GET, 'user_id'))) {
    section_head('IP log for user');
    $where['user_id'] = $_GET['user_id'];
} else {
    message_error('Must supply either IP or user ID');
}

echo '
    <table id="files" class="table table-striped table-hover">
      <thead>
        <tr>
          <th>Team name</th>
          <th>Hostname</th>
          <th>First used</th>
          <th>Last used</th>
          <th>Times used</th>
        </tr>
      </thead>
      <tbody>
    ';

$query = 'SELECT
           INET_NTOA(ipl.ip) AS ip,
           ipl.added,
           ipl.last_used,
           ipl.times_used,
           u.team_name,
           u.id AS user_id
        FROM ip_log AS ipl
        LEFT JOIN users AS u ON ipl.user_id = u.id
        ';

if (!empty($where)) {
    $query .= 'WHERE '.implode('=? AND ', array_keys($where)).'=? ';
}

$entries = db_query_fetch_all(
    $query,
    array_values($where)
);

foreach ($entries as $entry) {
    echo '
    <tr>
        <td>
            <a href="',CONFIG_SITE_ADMIN_URL,'list_ip_log?user_id=', htmlspecialchars($entry['user_id']), '">
                ', htmlspecialchars($entry['team_name']), '
            </a>
        </td>
        <td><a href="',CONFIG_SITE_ADMIN_URL,'list_ip_log?ip=',htmlspecialchars($entry['ip']),'">', htmlspecialchars(CONFIG_GET_IP_HOST_BY_ADDRESS ? gethostbyaddr($entry['ip']) : '<i>Lookup disabled in config</i>'), '</a></td>
        <td>', date_time($entry['added']), '</td>
        <td>', date_time($entry['last_used']), '</td>
        <td>', number_format($entry['times_used']), '</td>
    </tr>
    ';
}

echo '
      </tbody>
    </table>
     ';

foot();