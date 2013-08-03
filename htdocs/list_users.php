<?php

define('IN_FILE', true);
require('../include/general.inc.php');

enforceAuthentication(CONFIG_UC_MODERATOR);

head('Users');
managementMenu();
sectionHead('Users');

echo '
    <table id="files" class="table table-striped table-hover">
      <thead>
        <tr>
          <th>Team name</th>
          <th>Email</th>
          <th>Added</th>
          <th>Class</th>
          <th>Enabled</th>
          <th>Manage</th>
        </tr>
      </thead>
      <tbody>
    ';

$cat_stmt = $db->query('SELECT id, email, team_name, added, class, enabled FROM users ORDER BY team_name');
while($user = $cat_stmt->fetch(PDO::FETCH_ASSOC)) {
    echo '
    <tr>
        <td>',htmlspecialchars($user['team_name']),'</td>
        <td>',htmlspecialchars($user['email']),'</td>
        <td>',getDateTime($user['added']),'</td>
        <td>',getClassName($user['class']),'</td>
        <td>',($user['enabled'] ? 'Yes' : 'No'),'</td>
        <td>
            <a href="edit_user.php?id=',htmlspecialchars($user['id']),'" class="btn btn-mini btn-primary">Edit</a>
        </td>
    </tr>
    ';
}

echo '
      </tbody>
    </table>
     ';

foot();