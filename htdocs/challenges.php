<?php

define('IN_FILE', true);
require('../include/general.inc.php');

enforceAuthentication();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($_POST['action'] == 'submit_flag') {

        // make sure user isn't "accidentally" submitting a correct flag twice
        $stmt = $db->prepare('SELECT * FROM submissions WHERE user_id = :user_id AND challenge = :challenge AND correct = 1');
        $stmt->execute(array(':user_id' => $_SESSION['id'], ':challenge' => $_POST['challenge']));

        if ($stmt->rowCount()) {
            errorMessage('You may only submit a correct flag once :p');
        }

        // get challenge information
        $stmt = $db->prepare('SELECT flag FROM challenges WHERE id = :challenge');
        $stmt->execute(array(':challenge' => $_POST['challenge']));
        $challenge = $stmt->fetch(PDO::FETCH_ASSOC);

        $correct = false;
        if ($_POST['flag'] == $challenge['flag']) {
            $correct = true;
        }

        // insert submission
        $stmt = $db->prepare('INSERT INTO submissions (added, challenge, user_id, flag, correct) VALUES (UNIX_TIMESTAMP(), :challenge, :user_id, :flag, :correct)');
        $stmt->execute(array(':user_id' => $_SESSION['id'], ':flag' => $_POST['flag'], ':challenge' => $_POST['challenge'], ':correct' => $correct));

        header('location: challenges?success=' . ($correct ? '1' : '0'));
    }

    exit();
}

head('Challenges');

if (isset($_GET['success'])) {
    if ($_GET['success']) {
        echo '<div class="alert alert-success"><h1>Correct flag, you are awesome!</h1></div>';
    } else {
        echo '<div class="alert alert-error"><h1>Incorrect flag, try again.</h1></div>';
    }
}

$now = time();

$cat_stmt = $db->query('SELECT * FROM categories ORDER BY title');
while($category = $cat_stmt->fetch(PDO::FETCH_ASSOC)) {

    echo '

    <div class="page-header"><h2>', htmlspecialchars($category['title']), '</h2></div>';

    $row_counter = 0;

    $stmt = $db->prepare('
        SELECT
        c.id,
        c.title,
        c.description,
        c.available_from,
        c.available_until,
        c.points,
        s.correct
        FROM challenges AS c
        LEFT JOIN submissions AS s ON c.id = s.challenge AND s.user_id = :user_id AND correct = 1
        WHERE category = :category
        ORDER BY points ASC
    ');

    $stmt->execute(array(':user_id' => $_SESSION['id'], ':category' => $category['id']));

    while($challenge = $stmt->fetch(PDO::FETCH_ASSOC)) {

        echo '
        <div class="antihero-unit">
        <h5>',htmlspecialchars($challenge['title']), ' (', $challenge['points'], 'pts)', ($challenge['correct'] ? ' <img src="img/accept.png" alt="Completed!" title="Completed!" />' : '') ,'</h5>';

        $visible = true;

        if ($challenge['available_from'] && $now < $challenge['available_from']) {
            $visible = false;
        }

        if ($challenge['available_until'] && $now > $challenge['available_until']) {
            $visible = false;
        }


        if ($visible) {
            echo '
            <div class="description">
                ', formatText($challenge['description']),'
            </div>';

            $file_stmt = $db->prepare('SELECT id, title, size FROM files WHERE challenge = :id');
            $file_stmt->execute(array(':id' => $challenge['id']));

            if ($file_stmt->rowCount()) {
                echo '
                    <div class="files">
                    <h6>Provided files</h6>
                    <ul>
                ';

                while ($file = $file_stmt->fetch(PDO::FETCH_ASSOC)) {
                    echo '<li><a href="download?id=',htmlspecialchars($file['id']),'">',htmlspecialchars($file['title']),'</a> (',mkSize($file['size']),')</li>';
                }

                echo '
                    </ul>
                </div>
                ';
            }

            if (!$challenge['correct']) {

            echo '
            <form method="post" class="form-flag">
                <input name="flag" type="text" class="input-block-level" placeholder="Please enter flag for challenge: ',htmlspecialchars($challenge['title']),'">
                <input type="hidden" name="challenge" value="',htmlspecialchars($challenge['id']),'" />
                <input type="hidden" name="action" value="submit_flag" />
                <button class="btn btn-small btn-primary" type="submit">Submit flag</button>
            </form>
            ';
            }

        } else {
            echo '<i>Available in ',getTimeElapsed($challenge['available_from']-$now),' (from ', getDateTime($challenge['available_from']), ' until ', getDateTime($challenge['available_until']), ')</i>';
        }

        echo '</div>';
    }
}

foot();