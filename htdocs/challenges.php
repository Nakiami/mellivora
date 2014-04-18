<?php

require('../include/mellivora.inc.php');
require(CONFIG_PATH_THIRDPARTY . 'nbbc/nbbc.php');

enforce_authentication();

$time = time();

$bbc = new BBCode();
$bbc->SetEnableSmileys(false);

head('Challenges');

if (isset($_GET['success'])) {
    if ($_GET['success']) {
        echo '<div class="alert alert-success"><h1>Correct flag, you are awesome!</h1></div>';
    } else {
        echo '<div class="alert alert-danger"><h1>Incorrect flag, try again.</h1></div>';
    }
}

$cat_stmt = $db->query('SELECT id, title, description, available_from, available_until FROM categories ORDER BY title');
while($category = $cat_stmt->fetch(PDO::FETCH_ASSOC)) {

    if ($time > $category['available_from']) {
        section_head($category['title'], $bbc->parse($category['description']));
    }

    else {
        section_head('<i>Hidden category</i>', '', false);
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
            <div class="challenge-container">
                <h1><i>Hidden challenge worth ', number_format($challenge['points']), 'pts</i></h1>
                <i>Available in ',seconds_to_pretty_time($challenge['available_from']-$time),' (from ', date_time($challenge['available_from']), ' until ', date_time($challenge['available_until']), ')</i>
            </div>';

            continue;
        }

        $remaining_submissions = $challenge['num_attempts_allowed']-$challenge['num_submissions'];

        echo '
        <div class="challenge-container">
            <h1 class="challenge-head">
            <a href="challenge?id=',htmlspecialchars($challenge['id']),'">',htmlspecialchars($challenge['title']), '</a> (', number_format($challenge['points']), 'pts)';

            if ($challenge['correct']) {
                echo ' <img src="img/accept.png" alt="Completed!" title="Completed!" /> ', get_position_medal($challenge['pos']);
            } else if (!$remaining_submissions) {
                echo ' <img src="img/stop.png" alt="No more submissions allowed" title="No more submissions allowed" /> ';
            }

            echo '
            </h1>

            <div class="challenge-description">
                ',$bbc->parse($challenge['description']),'
            </div> <!-- / challenge-description -->';

        $file_stmt = $db->prepare('SELECT id, title, size FROM files WHERE challenge = :id');
        $file_stmt->execute(array(':id' => $challenge['id']));

        if ($file_stmt->rowCount()) {
            echo '

            <div class="challenge-files">
                <h6>Provided files</h6>
                <ul>
            ';

            while ($file = $file_stmt->fetch(PDO::FETCH_ASSOC)) {
                echo '      <li><a href="download?id=',htmlspecialchars($file['id']),'">',htmlspecialchars($file['title']),'</a> (',bytes_to_pretty_size($file['size']),')</li>';
            }

            echo '
                </ul>
            </div> <!-- / challenge-files -->';
        }

        // if we're already correct, or if the challenge has expired, remove the button
        if (!$challenge['correct'] && !($challenge['available_until'] && $time > $challenge['available_until'])) {

            if ($remaining_submissions) {

                $hint_stmt = $db->prepare('SELECT body FROM hints WHERE visible = 1 AND challenge = :id');
                $hint_stmt->execute(array(':id' => $challenge['id']));
                while ($hint = $hint_stmt->fetch(PDO::FETCH_ASSOC)) {
                    echo '
                <div class="alert alert-warning">
                <strong>Hint!</strong> ',$bbc->parse($hint['body']),'
                </div>
                ';
                }

                echo '
                <div class="challenge-submit">
                    <form method="post" class="form-flag" action="actions/challenges">
                        <textarea name="flag" type="text" class="form-control" placeholder="Please enter flag for challenge: ',htmlspecialchars($challenge['title']),'"></textarea>
                        <input type="hidden" name="challenge" value="',htmlspecialchars($challenge['id']),'" />
                        <input type="hidden" name="action" value="submit_flag" />
                        <p>
                            ',number_format($remaining_submissions),' submissions remaining. Available for another ', seconds_to_pretty_time($challenge['available_until']-$time),'.
                        </p>
                        <button class="btn btn-sm btn-primary" type="submit">Submit flag</button>
                    </form>
                </div>
                ';
            }

            else {
                echo '<div class="alert alert-danger">You have no remaining submission attempts. If you\'ve made an erroneous submission, please contact the organizers.</div>';
            }
        }

        echo '
        </div> <!-- / challenge-container -->

        ';
    }
}

foot();
