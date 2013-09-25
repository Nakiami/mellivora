<?php

define('IN_FILE', true);
require('../include/general.inc.php');

enforceAuthentication();

head('Hints');
sectionHead('Hints');

echo '
    <table id="files" class="table table-striped table-hover">
      <thead>
        <tr>
          <th>Challenge</th>
          <th>Added</th>
          <th>Hint</th>
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
    WHERE h.visible = 1
    ORDER BY h.id DESC
');
while($hint = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo '
    <tr>
        <td>',htmlspecialchars($hint['title']),'</td>
        <td>',getTimeElapsed($hint['added']),' ago</td>
        <td>',htmlspecialchars($hint['body']),'</td>
    </tr>
    ';
}

echo '
      </tbody>
    </table>
     ';

foot();