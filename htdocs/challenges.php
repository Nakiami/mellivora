<?php

define('IN_FILE', true);
require('../include/general.inc.php');
require(CONFIG_ABS_INCLUDE_PATH . 'nbbc/nbbc.php');

enforce_authentication();

$time = time(); // calling time() is expensive!

$bbc = new BBCode();
$bbc->SetEnableSmileys(false);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($_POST['action'] == 'submit_flag') {

        validate_id($_POST['challenge']);

        // make sure user isn't "accidentally" submitting a correct flag twice
        $stmt = $db->prepare('SELECT correct FROM submissions WHERE user_id = :user_id AND challenge = :challenge');
        $stmt->execute(array(':user_id' => $_SESSION['id'], ':challenge' => $_POST['challenge']));
        $num_attempts = 0;
        while ($submission = $stmt->fetch(PDO::FETCH_ASSOC)) {
            if ($submission['correct']) {
                message_error('You may only submit a correct flag once :p');
            }
            $num_attempts++;
        }

        // get challenge information
        $stmt = $db->prepare('SELECT flag, case_insensitive, available_from, available_until, num_attempts_allowed FROM challenges WHERE id = :challenge');
        $stmt->execute(array(':challenge' => $_POST['challenge']));
        $challenge = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($num_attempts >= $challenge['num_attempts_allowed']) {
            message_error('You\'ve already tried '.$challenge['num_attempts_allowed'].' times. Sorry!');
        }

        if ($challenge['available_from'] && $time < $challenge['available_from']) {
            message_error('This challenge hasn\'t started yet.');
        }

        if ($challenge['available_until'] && $time > $challenge['available_until']) {
            message_error('This challenge has expired.');
        }

        $correct = false;
        if ($challenge['case_insensitive']) {
            if (strcasecmp($_POST['flag'], $challenge['flag']) == 0) {
                $correct = true;
            }
        } else {
            if (strcmp($_POST['flag'], $challenge['flag']) == 0) {
                $correct = true;
            }
        }

        db_insert(
            'submissions',
            array(
                'added'=>$time,
                'challenge'=>$_POST['challenge'],
                'user_id'=>$_SESSION['id'],
                'flag'=>$_POST['flag'],
                'correct'=>$correct
            )
        );

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

    if ($time > $category['available_from']) {
        section_head($category['title']);
        echo '<p>',$bbc->parse($category['description']),'</p>';
    }

    else {
        section_head('<i>Hidden category</i>', false);
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
        ((SELECT COUNT(*) FROM submissions AS ss WHERE ss.correct = 1 AND ss.added < s.added AND ss.challenge=s.challenge)+1) AS pos,
        COUNT(si.id) AS num_submissions
        FROM challenges AS c
        LEFT JOIN submissions AS s ON c.id = s.challenge AND s.user_id = :user_id AND correct = 1
        LEFT JOIN submissions AS si ON si.challenge = c.id AND si.user_id = :user_id_again
        WHERE category = :category
        GROUP BY c.id
        ORDER BY c.points ASC, c.id ASC
    ');

    $stmt->execute(array(':user_id' => $_SESSION['id'], ':user_id_again' => $_SESSION['id'], ':category' => $category['id']));

    while($challenge = $stmt->fetch(PDO::FETCH_ASSOC)) {

        // if the challenge isn't available yet
        if ($challenge['available_from'] && $time < $challenge['available_from']) {
            echo '
            <div class="antihero-unit">
                <h5><i>Hidden challenge worth ', number_format($challenge['points']), 'pts</i></h5>
                <i>Available in ',seconds_to_pretty_time($challenge['available_from']-$time),' (from ', get_date_time($challenge['available_from']), ' until ', get_date_time($challenge['available_until']), ')</i>
            </div>';

            continue;
        }

        $remaining_submissions = $challenge['num_attempts_allowed']-$challenge['num_submissions'];

        echo '
        <div class="antihero-unit">
        <h5><a href="challenge?id=',htmlspecialchars($challenge['id']),'">',htmlspecialchars($challenge['title']), '</a> (', number_format($challenge['points']), 'pts)';

        if ($challenge['correct']) {
            echo ' <img src="img/accept.png" alt="Completed!" title="Completed!" /> ', get_position_medal($challenge['pos']);
        } else if (!$remaining_submissions) {
            echo ' <img src="img/stop.png" alt="No more submissions allowed" title="No more submissions allowed" /> ';
        }

        echo '
        </h5>

        <div class="description">
            ',$bbc->parse($challenge['description']),'
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
                echo '<li><a href="download?id=',htmlspecialchars($file['id']),'">',htmlspecialchars($file['title']),'</a> (',mk_size($file['size']),')</li>';
            }

            echo '
                </ul>
            </div>
            ';
        }

        // if we're already correct, or if the challenge has expired, remove the button
        if (!$challenge['correct'] && !($challenge['available_until'] && $time > $challenge['available_until'])) {

            if ($remaining_submissions) {

                $hint_stmt = $db->prepare('SELECT body FROM hints WHERE visible = 1 AND challenge = :id');
                $hint_stmt->execute(array(':id' => $challenge['id']));
                while ($hint = $hint_stmt->fetch(PDO::FETCH_ASSOC)) {
                    echo '
                <div class="alert alert-block">
                <strong>Hint!</strong> ',$bbc->parse($hint['body']),'
                </div>
                ';
                }

                echo '
                <form method="post" class="form-flag">
                    <input name="flag" type="text" class="input-block-level" placeholder="Please enter flag for challenge: ',htmlspecialchars($challenge['title']),'">
                    <input type="hidden" name="challenge" value="',htmlspecialchars($challenge['id']),'" />
                    <input type="hidden" name="action" value="submit_flag" />
                    <p>
                        ',number_format($remaining_submissions),' submissions remaining. Available for another ', seconds_to_pretty_time($challenge['available_until']-$time),'.
                    </p>
                    <button class="btn btn-small btn-primary" type="submit">Submit flag</button>
                </form>
                ';
            }

            else {
                echo '<div class="alert alert-danger">You have no remaining submission attempts. If you\'ve made an erroneous submission, please contact the organizers.</div>';
            }
        }

        echo '</div>';
    }
}

foot();