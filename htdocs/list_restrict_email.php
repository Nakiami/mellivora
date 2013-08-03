<?php

define('IN_FILE', true);
require('../include/general.inc.php');

enforceAuthentication(CONFIG_UC_MODERATOR);

head('Email signup rules');
managementMenu();
sectionHead('Email signup rules');

echo '

    Rules in list below are applied top-down. Rules further down on the list override rules above.
    List is ordered by "priority". A higher "priority" value puts a rule further down the list.
    Rules must be of format: "xxx@yyy", "*@yyy", or "xxx@*".

    <table id="rules" class="table table-striped table-hover">
      <thead>
        <tr>
          <th>Rule</th>
          <th>Added</th>
          <th>Added by</th>
          <th>Type</th>
          <th>Priority</th>
          <th>Enabled</th>
          <th>Manage</th>
        </tr>
      </thead>
      <tbody>
    ';

$stmt = $db->query('
    SELECT
    re.id,
    re.added,
    re.added_by,
    re.rule,
    re.enabled,
    re.white,
    re.priority,
    u.team_name
    FROM restrict_email AS re
    LEFT JOIN users AS u ON re.added_by = u.id
    ORDER BY re.priority ASC
    ');
while($rule = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo '
    <tr>
        <td>',htmlspecialchars($rule['rule']),'</td>
        <td>',getDateTime($rule['added']),'</td>
        <td>',htmlspecialchars($rule['team_name']),'</td>
        <td>
            ',($rule['white'] ?
            '<img src="img/accept.png" alt="Whitelisted" title="Whitelisted" />' :
            '<img src="img/stop.png" alt="Blacklisted" title="Blacklisted" />'),'
        </td>
        <td>',number_format($rule['priority']),'</td>
        <td>',($rule['enabled'] ? 'Yes' : 'No'),'</td>
        <td>
            <a href="edit_restrict_email.php?id=',htmlspecialchars($rule['id']),'" class="btn btn-mini btn-primary">Edit</a>
        </td>
    </tr>
    ';
}

echo '
      </tbody>
    </table>
     ';

foot();