<?php

require('../include/mellivora.inc.php');
require(CONFIG_PATH_THIRDPARTY . 'nbbc/nbbc.php');

enforce_authentication();

$time = time();

$bbc = new BBCode();
$bbc->SetEnableSmileys(false);

head('Challenges');

if (isset($_GET['status'])) {
    if ($_GET['status']=='correct') {
        message_inline_green('<h1>Correct flag, you are awesome!</h1>', false);
    } else if ($_GET['status']=='incorrect') {
        message_inline_red('<h1>Incorrect flag, try again.</h1>', false);
    } else if ($_GET['status']=='manual') {
        message_inline_blue('<h1>Your submission is awaiting manual marking.</h1>', false);
    }
}

$categories = db_select_all(
    'categories',
    array(
        'id',
        'title',
        'description',
        'available_from',
        'available_until'
    ),
    null,
    'title'
);

foreach ($categories as $category) {

    if ($time > $category['available_from']) {
        section_head($category['title'], $bbc->parse($category['description']));
    }

    else {
        section_head('<i>Hidden category</i>', '', false);
    }

    $challenges = db_query_fetch_all('
        SELECT
           c.id,
           c.title,
           c.description,
           c.available_from,
           c.available_until,
           c.points,
           c.num_attempts_allowed,
           c.automark,
           s.correct,
           s.marked,
           ((SELECT COUNT(*) FROM submissions AS ss WHERE ss.correct = 1 AND ss.added < s.added AND ss.challenge=s.challenge)+1) AS pos,
           COUNT(si.id) AS num_submissions
        FROM challenges AS c
        LEFT JOIN submissions AS s ON c.id = s.challenge AND s.user_id = :user_id AND correct = 1
        LEFT JOIN submissions AS si ON si.challenge = c.id AND si.user_id = :user_id_again
        WHERE category = :category
        GROUP BY c.id
        ORDER BY c.points ASC, c.id ASC',
        array(
            'user_id'=>$_SESSION['id'],
            'user_id_again'=>$_SESSION['id'],
            'category'=>$category['id']
        )
    );

    $row_counter = 0;
    foreach($challenges as $challenge) {

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
                echo ' <img src="'.CONFIG_SITE_URL.'img/accept.png" alt="Completed!" title="Completed!" /> ', get_position_medal($challenge['pos']);
            } else if (!$remaining_submissions) {
                echo ' <img src="'.CONFIG_SITE_URL.'img/stop.png" alt="No more submissions allowed" title="No more submissions allowed" /> ';
            }

            echo '
            </h1>

            <div class="challenge-description">
                ',$bbc->parse($challenge['description']),'
            </div> <!-- / challenge-description -->';

        if (cache_start('files_' . $challenge['id'], CONFIG_CACHE_TIME_FILES)) {
            $files = db_select_all(
                'files',
                array(
                    'id',
                    'title',
                    'size'
                ),
                array('challenge' => $challenge['id'])
            );

            if (count($files)) {
                echo '

                <div class="challenge-files">
                    <h6>Provided files</h6>
                    <ul>
                ';

                foreach ($files as $file) {
                    echo '      <li><a href="download?id=',htmlspecialchars($file['id']),'">',htmlspecialchars($file['title']),'</a> (',bytes_to_pretty_size($file['size']),')</li>';
                }

                echo '
                    </ul>
                </div> <!-- / challenge-files -->';
            }

            cache_end('files_' . $challenge['id']);
        }

        // if we're already correct, or if the challenge has expired, remove the button
        if (!$challenge['correct'] && !($challenge['available_until'] && $time > $challenge['available_until'])) {

            if ($remaining_submissions) {

                if (cache_start('hints_challenge_' . $challenge['id'], CONFIG_CACHE_TIME_HINTS)) {
                    $hints = db_select_all(
                        'hints',
                        array('body'),
                        array(
                            'visible' => 1,
                            'challenge' => $challenge['id']
                        )
                    );

                    foreach ($hints as $hint) {
                        message_inline_yellow('<strong>Hint!</strong> ' . $bbc->parse($hint['body']), false);
                    }

                    cache_end('hints_challenge_' . $challenge['id']);
                }

                if ($challenge['num_submissions'] && !$challenge['automark'] && !$challenge['marked']) {
                    message_inline_blue('Your submission is awaiting manual marking.');
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
            // no remaining submissions
            else {
                message_inline_red("You have no remaining submission attempts. If you've made an erroneous submission, please contact the organizers.");
            }
        }

        echo '
        </div> <!-- / challenge-container -->

        ';
    }
}

foot();