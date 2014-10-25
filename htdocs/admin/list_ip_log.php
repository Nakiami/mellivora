<?php

require('../../include/mellivora.inc.php');

enforce_authentication(CONFIG_UC_MODERATOR);

head('IP log');
menu_management();

// show a users IP log
if (isset($_GET['id']) && is_valid_id($_GET['id'])) {
    $user = db_select_one(
        'users',
        array('team_name'),
        array('id' => $_GET['id'])
    );

    section_head('IP log for team: <a href="'.CONFIG_SITE_URL.'user?id='.$_GET['id'].'">'.htmlspecialchars($user['team_name']).'</a>', '', false);

    user_ip_log($_GET['id']);
}

// display users sharing an IP
else if (isset($_GET['ip']) && is_valid_ip($_GET['ip'])) {

    section_head('Teams using IP ' . $_GET['ip']);

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

    $entries = db_query_fetch_all('
        SELECT
           INET_NTOA(ipl.ip) AS ip,
           ipl.added,
           ipl.last_used,
           ipl.times_used,
           u.team_name,
           u.id AS user_id
        FROM ip_log AS ipl
        LEFT JOIN users AS u ON ipl.user_id = u.id
        WHERE ipl.ip=INET_ATON(:ip)',
        array('ip'=>$_GET['ip'])
    );

    $host = CONFIG_GET_IP_HOST_BY_ADDRESS ? gethostbyaddr($_GET['ip']) : '<i>Lookup disabled in config</i>';

    foreach($entries as $entry) {
        echo '
    <tr>
        <td>
            <a href="list_ip_log.php?id=',htmlspecialchars($entry['user_id']),'">
                ',htmlspecialchars($entry['team_name']),'
            </a>
        </td>
        <td>',$host,'</td>
        <td>',date_time($entry['added']),'</td>
        <td>',date_time($entry['last_used']),'</td>
        <td>',number_format($entry['times_used']),'</td>
    </tr>
    ';
    }

    echo '
      </tbody>
    </table>
     ';
}

foot();