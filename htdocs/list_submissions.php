<?php

define('IN_FILE', true);
require('../include/general.inc.php');

enforce_authentication(CONFIG_UC_MODERATOR);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    validate_id($_POST['id']);

    if ($_POST['action'] == 'delete') {

        $stmt = $db->prepare('DELETE FROM submissions WHERE id=:id');
        $stmt->execute(array(':id'=>$_POST['id']));

        header('location: list_submissions.php?generic_success=1');
        exit();
    }

    else if ($_POST['action'] == 'mark_incorrect') {

        db_update('submissions',array('correct'=>0), array('id'=>$_POST['id']));

        header('location: list_submissions.php?generic_success=1');
        exit();
    }

    else if ($_POST['action'] == 'mark_correct') {

        db_update('submissions',array('correct'=>1), array('id'=>$_POST['id']));

        header('location: list_submissions.php?generic_success=1');
        exit();
    }
}

head('Submissions');
menu_management();
section_head('Submissions');

echo '
    <table id="files" class="table table-striped table-hover">
      <thead>
        <tr>
          <th>Challenge</th>
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
    s.flag,
    c.id AS challenge_id,
    c.title AS challenge_title
    FROM
    submissions AS s
    LEFT JOIN users AS u on s.user_id = u.id
    LEFT JOIN challenges AS c ON c.id = s.challenge
    ORDER BY s.added DESC
');
while($submission = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo '
    <tr>
        <td><a href="challenge.php?id=',htmlspecialchars($submission['challenge_id']),'">',htmlspecialchars($submission['challenge_title']),'</a></td>
        <td><a href="user.php?id=',htmlspecialchars($submission['user_id']),'">',htmlspecialchars($submission['team_name']),'</a></td>
        <td>',get_time_elapsed($submission['added']),' ago</td>
        <td>',htmlspecialchars($submission['flag']),'</td>
        <td>
            ',($submission['correct'] ?
                '<img src="img/accept.png" alt="Correct!" title="Correct!" />' :
                '<img src="img/stop.png" alt="Wrong!" title="Wrong!" />'),'
        </td>
        <td>
            <form method="post" style="padding:0;margin:0;display:inline;">
                <input type="hidden" name="action" value="',($submission['correct'] ? 'mark_incorrect' : 'mark_correct'),'" />
                <input type="hidden" name="id" value="',htmlspecialchars($submission['id']),'" />
                <button type="submit" class="btn btn-small btn-',($submission['correct'] ? 'warning' : 'success'),'">Mark ',($submission['correct'] ? 'incorrect' : 'correct'),'</button>
            </form>

            <form method="post" style="padding:0;margin:0;display:inline;">
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