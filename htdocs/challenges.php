<?php

define('IN_FILE', true);
require('../include/general.inc.php');
require(CONFIG_ABS_PATH . 'include/nbbc/nbbc.php');

enforceAuthentication();

$now = time(); // calling time() is expensive!

$bbc = new BBCode();
$bbc->SetEnableSmileys(false);


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($_POST['action'] == 'submit_flag') {

        // make sure user isn't "accidentally" submitting a correct flag twice
        $stmt = $db->prepare('SELECT correct FROM submissions WHERE user_id = :user_id AND challenge = :challenge');
        $stmt->execute(array(':user_id' => $_SESSION['id'], ':challenge' => $_POST['challenge']));
        $num_attempts = 0;
        while ($submission = $stmt->fetch(PDO::FETCH_ASSOC)) {
            if ($submission['correct']) {
                errorMessage('You may only submit a correct flag once :p');
            }
            $num_attempts++;
        }

        // get challenge information
        $stmt = $db->prepare('SELECT flag, available_from, available_until, num_attempts_allowed FROM challenges WHERE id = :challenge');
        $stmt->execute(array(':challenge' => $_POST['challenge']));
        $challenge = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($num_attempts >= $challenge['num_attempts_allowed']) {
            errorMessage('You\'ve already tried '.$challenge['num_attempts_allowed'].' times. Sorry!');
        }

        if ($challenge['available_from'] && $now < $challenge['available_from']) {
            errorMessage('This challenge hasn\'t started yet.');
        }

        if ($challenge['available_until'] && $now > $challenge['available_until']) {
            errorMessage('This challenge has expired.');
        }

        $correct = false;
        if ($_POST['flag'] == $challenge['flag']) {
            $correct = true;
        }

        // insert submission
        $stmt = $db->prepare('
            INSERT INTO submissions (
            added,
            challenge,
            user_id,
            flag,
            correct,
            pos
            ) VALUES (
            UNIX_TIMESTAMP(),
            :challenge,
            :user_id,
            :flag,
            :correct,
            ((SELECT COUNT(*) FROM submissions AS s WHERE s.challenge=:challenge_again AND s.correct = 1) + 1)
            )
        ');
        $stmt->execute(array(':user_id' => $_SESSION['id'], ':flag' => $_POST['flag'], ':challenge' => $_POST['challenge'], ':correct' => $correct, ':challenge_again' => $_POST['challenge']));

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

$cat_stmt = $db->query('SELECT id, title, description, available_from, available_until FROM categories ORDER BY title');
while($category = $cat_stmt->fetch(PDO::FETCH_ASSOC)) {

    sectionHead($category['title']);

    if ($now > $category['available_from']) {
        echo '<p>',$bbc->parse($category['description']),'</p>';
    }

    $row_counter = 0;
    $stmt = $db->prepare('
        SELECT
        c.id,
        c.title,
        c.description,
        c.available_from,
        c.available_until,
        c.points,
        c.num_attempts_allowed,
        s.correct,
        s.pos
        FROM challenges AS c
        LEFT JOIN submissions AS s ON c.id = s.challenge AND s.user_id = :user_id AND correct = 1
        WHERE category = :category
        ORDER BY c.points ASC, c.id ASC
    ');

    $stmt->execute(array(':user_id' => $_SESSION['id'], ':category' => $category['id']));

    while($challenge = $stmt->fetch(PDO::FETCH_ASSOC)) {

        // if the challenge isn't available yet
        if ($challenge['available_from'] && $now < $challenge['available_from']) {
            echo '
            <div class="antihero-unit">
                <h5><i>Hidden challenge worth ', number_format($challenge['points']), 'pts</i></h5>
                <i>Available in ',secondsToPrettyTime($challenge['available_from']-$now),' (from ', getDateTime($challenge['available_from']), ' until ', getDateTime($challenge['available_until']), ')</i>
            </div>';
            continue;
        }

        echo '
        <div class="antihero-unit">
        <h5>',htmlspecialchars($challenge['title']), ' (', number_format($challenge['points']), 'pts)';

        if ($challenge['correct']) {
            echo ' <img src="img/accept.png" alt="Completed!" title="Completed!" /> ', getPositionMedal($challenge['pos']);
        }

        echo '
        </h5>

        <div class="description">
            ', $bbc->parse($challenge['description']),'
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

        // if we're already correct, or if the challenge has expired, remove the button
        if (!$challenge['correct'] && !($challenge['available_until'] && $now > $challenge['available_until'])) {

        echo '
        <form method="post" class="form-flag">
            <input name="flag" type="text" class="input-block-level" placeholder="Please enter flag for challenge: ',htmlspecialchars($challenge['title']),'">
            <input type="hidden" name="challenge" value="',htmlspecialchars($challenge['id']),'" />
            <input type="hidden" name="action" value="submit_flag" />
            <p>
                Maximum number of guesses: ',number_format($challenge['num_attempts_allowed']),'. Available for another ', secondsToPrettyTime($challenge['available_until']-$now),'.
            </p>
            <button class="btn btn-small btn-primary" type="submit">Submit flag</button>
        </form>
        ';
        }

        echo '</div>';
    }
}

foot();