<?php

define('IN_FILE', true);
require('../include/general.inc.php');

enforceAuthentication(CONFIG_UC_MODERATOR);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if ($_POST['action'] == 'new') {

       $inserted = sqlInsert(
          'hints',
          array(
             'added'=>time(),
             'added_by'=>$_SESSION['id'],
             'challenge'=>$_POST['challenge'],
             'visible'=>$_POST['visible'],
             'body'=>$_POST['body']
          )
       );

        if ($inserted) {
            header('location: edit_hint.php?id='.$inserted);
            exit();
        } else {
            errorMessage('Could not insert new hint:' . $stmt->errorCode());
        }
    }
}

head('Site management');
managementMenu();
sectionSubHead('New hint');

echo '
<form class="form-horizontal" method="post">

    <div class="control-group">
        <label class="control-label" for="description">Body</label>
        <div class="controls">
            <textarea id="body" name="body" class="input-block-level" rows="10"></textarea>
        </div>
    </div>
    ';

echo '
    <div class="control-group">
        <label class="control-label" for="challenge">Challenge</label>
        <div class="controls">

        <select id="challenge" name="challenge">';
$stmt = $db->query('SELECT
                      ch.id,
                      ch.title,
                      ca.title AS category
                    FROM challenges AS ch
                    LEFT JOIN categories AS ca ON ca.id = ch.category
                    ORDER BY ca.title, ch.title
                    ');
$category = '';
while ($challenge = $stmt->fetch(PDO::FETCH_ASSOC)) {
    if ($category != $challenge['category']) {
        if ($category) {
            echo '</optgroup>';
        }
        echo '<optgroup label="',htmlspecialchars($challenge['category']),'">';
    }

    echo '<option value="',htmlspecialchars($challenge['id']),'"',(isset($_GET['id']) && $challenge['id'] == $_GET['id'] ? ' selected="selected"' : ''),'>', htmlspecialchars($challenge['title']), '</option>';

    $category = $challenge['category'];
}
echo '
        </optgroup>
        </select>

        </div>
    </div>
    ';

echo '
        <div class="control-group">
            <label class="control-label" for="visible">Visible</label>
            <div class="controls">
                <input type="checkbox" id="visible" name="visible" value="1" checked="checked" />
            </div>
        </div>

    <input type="hidden" name="action" value="new" />

    <div class="control-group">
        <label class="control-label" for="save"></label>
        <div class="controls">
            <button type="submit" id="save" class="btn btn-primary">Create hint</button>
        </div>
    </div>

</form>
';

foot();