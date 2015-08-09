<?php

require('../../include/mellivora.inc.php');

enforce_authentication(CONST_USER_CLASS_MODERATOR);

head('Hints');
menu_management();
section_head('Hints',button_link('Add new hint', 'new_hint'), false);

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

$hints = db_query_fetch_all('
    SELECT
       h.id,
       h.added,
       h.body,
       c.title
    FROM hints AS h
    LEFT JOIN challenges AS c ON c.id = h.challenge'
);

foreach($hints as $hint) {
    echo '
    <tr>
        <td>',htmlspecialchars($hint['title']),'</td>
        <td>',date_time($hint['added']),'</td>
        <td>',htmlspecialchars($hint['body']), '</td>
        <td><a href="edit_hint.php?id=',$hint['id'],'" class="btn btn-xs btn-primary">Edit</a></td>
    </tr>
    ';
}

echo '
      </tbody>
    </table>
     ';

foot();