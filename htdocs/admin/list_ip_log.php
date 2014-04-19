<?php

require('../../include/mellivora.inc.php');

enforce_authentication(CONFIG_UC_MODERATOR);

head('IP log');
menu_management();

// show a users IP log
if (isset($_GET['id']) && valid_id($_GET['id'])) {

    $user = db_select(
        'users',
        array('team_name'),
        array('id'=>$_GET['id']),
        false
    );

    section_head('IP log for team: ' . $user['team_name']);

    echo '
        <table id="files" class="table table-striped table-hover">
          <thead>
            <tr>
              <th>IP</th>
              <th>Hostname</th>
              <th>First used</th>
              <th>Last used</th>
              <th>Times used</th>
            </tr>
          </thead>
          <tbody>
        ';

    $entries = db_select(
        'ip_log',
        array(
            'INET_NTOA(ip) AS ip',
            'added',
            'last_used',
            'times_used'
        ),
        array('user_id'=>$_GET['id'])
    );

    foreach($entries as $entry) {
        echo '
        <tr>
            <td><a href="list_ip_log.php?ip=',htmlspecialchars($entry['ip']),'">',htmlspecialchars($entry['ip']),'</a></td>
            <td>',gethostbyaddr($entry['ip']),'</td>
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

// display users sharing an IP
else if (isset($_GET['ip']) && valid_ip($_GET['ip'])) {

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

    $entries = db_query('
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

    foreach($entries as $entry) {
        echo '
    <tr>
        <td>
            <a href="list_ip_log.php?id=',htmlspecialchars($entry['user_id']),'">
                ',htmlspecialchars($entry['team_name']),'
            </a>
        </td>
        <td>',gethostbyaddr($entry['ip']),'</td>
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