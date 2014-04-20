<?php

require('../../include/mellivora.inc.php');

enforce_authentication(CONFIG_UC_MODERATOR);

head('Email signup rules');
menu_management();
section_head('Email signup rules');

message_inline_blue('Rules in list below are applied top-down. Rules further down on the list override rules above.
                     List is ordered by "priority". A higher "priority" value puts a rule further down the list.
                     Rules are PCRE regex. Example: ^.+@.+$');

echo '
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

$rules = db_query_fetch_all('
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
    ORDER BY re.priority ASC'
);

foreach($rules as $rule) {
    echo '
    <tr>
        <td>',htmlspecialchars($rule['rule']),'</td>
        <td>',date_time($rule['added']),'</td>
        <td>',htmlspecialchars($rule['team_name']),'</td>
        <td>
            ',($rule['white'] ?
            '<img src="'.CONFIG_SITE_URL.'img/accept.png" alt="Whitelisted" title="Whitelisted" />' :
            '<img src="'.CONFIG_SITE_URL.'img/stop.png" alt="Blacklisted" title="Blacklisted" />'),'
        </td>
        <td>',number_format($rule['priority']),'</td>
        <td>',($rule['enabled'] ? 'Yes' : 'No'), '</td>
        <td>
            <a href="edit_restrict_email.php?id=',htmlspecialchars($rule['id']),'" class="btn btn-xs btn-primary">Edit</a>
        </td>
    </tr>
    ';
}

echo '
      </tbody>
    </table>
     ';

foot();