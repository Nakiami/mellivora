<?php

define('IN_FILE', true);
require('../include/general.inc.php');

enforceAuthentication(CONFIG_UC_MODERATOR);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if ($_POST['action'] == 'delete' && isValidID($_POST['id'])) {

        $stmt = $db->prepare('DELETE FROM submissions WHERE id=:id');
        $stmt->execute(array(':id'=>$_POST['id']));

        header('location: list_submissions.php?generic_success=1');
        exit();
    }
}

head('Submissions');
managementMenu();
sectionHead('Submissions');

echo '
    <table id="files" class="table table-striped table-hover">
      <thead>
        <tr>
          <th>Team name</th>
          <th>Added</th>
          <th>Flag</th>
          <th>Correct</th>
          <th>Manage</th>
        </tr>
      </thead>
      <tbody>
    ';

$stmt = $db->query('
    SELECT
    s.id,
    u.id AS user_id,
    u.team_name,
    s.added,
    s.correct,
    s.flag
    FROM
    submissions AS s
    LEFT JOIN users AS u on s.user_id = u.id
    ORDER BY s.added DESC
');
while($submission = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo '
    <tr>
        <td>',htmlspecialchars($submission['team_name']),'</td>
        <td>',getDateTime($submission['added']),'</td>
        <td>',htmlspecialchars($submission['flag']),'</td>
        <td>
            ',($submission['correct'] ?
                '<img src="img/accept.png" alt="Correct!" title="Correct!" />' :
                '<img src="img/stop.png" alt="Wrong!" title="Wrong!" />'),'
        </td>
        <td>
            <form method="post" style="padding:0;margin:0;">
                <input type="hidden" name="action" value="delete" />
                <input type="hidden" name="id" value="',htmlspecialchars($submission['id']),'" />
                <button type="submit" class="btn btn-small btn-danger">Delete</button>
            </form>
        </td>
    </tr>
    ';
}

echo '
      </tbody>
    </table>
     ';

foot();