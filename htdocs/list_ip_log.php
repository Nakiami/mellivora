<?php

define('IN_FILE', true);
require('../include/general.inc.php');

enforceAuthentication(CONFIG_UC_MODERATOR);

head('IP log');
managementMenu();

// show a users IP log
if (isset($_GET['id']) && isValidID($_GET['id'])) {

    $stmt = $db->prepare('SELECT team_name FROM users WHERE id=:id');
    $stmt->execute(array('id'=>$_GET['id']));
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    sectionHead('IP log for team: ' . $user['team_name']);

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

    $stmt = $db->prepare('
        SELECT
        INET_NTOA(ipl.ip) AS ip,
        ipl.added,
        ipl.last_used,
        ipl.times_used
        FROM ip_log AS ipl
        WHERE ipl.user_id=:user_id
        ');

    $stmt->execute(array(':user_id' => $_GET['id']));
    while($entry = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo '
        <tr>
            <td><a href="list_ip_log.php?ip=',htmlspecialchars($entry['ip']),'">',htmlspecialchars($entry['ip']),'</a></td>
            <td>',gethostbyaddr($entry['ip']),'</td>
            <td>',getDateTime($entry['added']),'</td>
            <td>',getDateTime($entry['last_used']),'</td>
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
else if (isset($_GET['ip']) && isValidIP($_GET['ip'])) {

    sectionHead('Teams using IP ' . $_GET['ip']);

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

    $stmt = $db->prepare('
    SELECT
    INET_NTOA(ipl.ip) AS ip,
    ipl.added,
    ipl.last_used,
    ipl.times_used,
    u.team_name,
    u.id AS user_id
    FROM ip_log AS ipl
    LEFT JOIN users AS u ON ipl.user_id = u.id
    WHERE ipl.ip=INET_ATON(:ip)
    ');

    $stmt->execute(array(':ip' => $_GET['ip']));
    while($entry = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo '
    <tr>
        <td>
            <a href="list_ip_log.php?id=',htmlspecialchars($entry['user_id']),'">
                ',htmlspecialchars($entry['team_name']),'
            </a>
        </td>
        <td>',gethostbyaddr($entry['ip']),'</td>
        <td>',getDateTime($entry['added']),'</td>
        <td>',getDateTime($entry['last_used']),'</td>
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