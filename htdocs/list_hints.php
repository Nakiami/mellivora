<?php

require('../include/mellivora.inc.php');

enforce_authentication(CONFIG_UC_MODERATOR);

head('Hints');
menu_management();
section_head('Hints');

echo '
    <table id="hints" class="table table-striped table-hover">
      <thead>
        <tr>
          <th>Challenge</th>
          <th>Added</th>
          <th>Hint</th>
          <th>Manage</th>
        </tr>
      </thead>
      <tbody>
    ';

$stmt = $db->query('
    SELECT
    h.id,
    h.added,
    h.body,
    c.title
    FROM hints AS h
    LEFT JOIN challenges AS c ON c.id = h.challenge
');
while($hint = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo '
    <tr>
        <td>',htmlspecialchars($hint['title']),'</td>
        <td>',date_time($hint['added']),'</td>
        <td>',htmlspecialchars($hint['body']),'</td>
        <td><a href="edit_hint.php?id=',htmlspecialchars(short_description($hint['id'], 70)),'" class="btn btn-xs btn-primary">Edit</a></td>
    </tr>
    ';
}

echo '
      </tbody>
    </table>
     ';

foot();