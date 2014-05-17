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
    'title ASC'
);

if (isset($_GET['category'])) {
    // select our chosen category
    $current_category = db_select_one(
        'categories',
        array(
            'id',
            'title',
            'description',
            'available_from',
            'available_until'
        ),
        array(
            'id'=>$_GET['category']
        )
    );

    if (!$current_category) {
        message_error('No category found with that ID', false);
    }

} else {
    // if no category is selected, display
    // the first available category
    foreach ($categories as $cat) {
        if ($time > $cat['available_from'] && $time < $cat['available_until']) {
            $current_category = $cat;
            break;
        }
    }
    // if no category has been made available
    // we'll just set it to the first one
    // alphabetically and display an error
    // message
    if (!isset($current_category)) {
        $current_category = $categories[0];
    }
}

// write out our categories menu
echo '<div id="categories-menu">
<ul class="nav nav-tabs" id="categories-menu">';
foreach ($categories as $cat) {
    if ($time < $cat['available_from'] || $time > $cat['available_until']) {
        echo '<li class="disabled">
        <a title="Available ',date_time($cat['available_from'], 5),' ('.time_remaining($cat['available_from']).' from now) until ',date_time($cat['available_until'], 5),' ('.time_remaining($cat['available_until']).' from now)">',htmlspecialchars($cat['title']),'</a>
        </li>';
    } else {
        echo '<li ',($current_category['id'] == $cat['id'] ? ' class="active"' : ''),'><a href="',CONFIG_SITE_URL,'challenges?category=',htmlspecialchars($cat['id']),'">',htmlspecialchars($cat['title']),'</a></li>';
    }
}
echo '</ul>
</div>';

// check that the category is actually available for display
if ($time < $current_category['available_from'] || $time > $current_category['available_until']) {
    message_generic('Category unavailable','This category is not available. It is open from ' . date_time($current_category['available_from']) . ' ('. time_remaining($current_category['available_from']) .' from now) until ' . date_time($current_category['available_until']) . ' ('. time_remaining($current_category['available_from']) .' from now)', false);
}

// write out the category description, if one exists
if ($current_category['description']) {
    echo '<div id="category-description">', $bbc->parse($current_category['description']), '</div>';
}

// get all the challenges for the selected category
$challenges = db_query_fetch_all('
    SELECT
       c.id,
       c.title,
       c.description,
       c.available_from,
       c.available_until,
       c.points,
       c.num_attempts_allowed,
       c.min_seconds_between_submissions,
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
        'category'=>$current_category['id']
    )
);

foreach($challenges as $challenge) {

    // if the challenge isn't available yet, display a message
    // and continue to next challenge
    if ($time < $challenge['available_from']) {
        echo '
        <div class="challenge-container">
            <h1><i>Hidden challenge worth ', number_format($challenge['points']), 'pts</i></h1>
            <i>Available in ',time_remaining($challenge['available_from']),' (from ', date_time($challenge['available_from']), ' until ', date_time($challenge['available_until']), ')</i>
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

    echo '</h1>';

    // write out challenge description
    if ($challenge['description']) {
        echo '
        <div class="challenge-description">
            ',$bbc->parse($challenge['description']),'
        </div> <!-- / challenge-description -->';
    }

    // write out files
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

    // only show the hints and flag submission form if we're
    // not already correct and if the challenge hasn't expired
    if (!$challenge['correct'] && $time < $challenge['available_until']) {

        // write out hints
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

        if ($remaining_submissions) {

            if ($challenge['num_submissions'] && !$challenge['automark'] && !$challenge['marked']) {
                message_inline_blue('Your submission is awaiting manual marking.');
            }

            echo '
            <div class="challenge-submit">
                <form method="post" class="form-flag" action="actions/challenges">
                    <textarea name="flag" type="text" class="form-control" placeholder="Please enter flag for challenge: ',htmlspecialchars($challenge['title']),'"></textarea>
                    <input type="hidden" name="challenge" value="',htmlspecialchars($challenge['id']),'" />
                    <input type="hidden" name="action" value="submit_flag" />';

            form_xsrf_token();

            if (CONFIG_RECAPTCHA_ENABLE_PRIVATE) {
                display_captcha();
            }

            echo '  <p>
                        ',($challenge['min_seconds_between_submissions'] ? 'Minimum of '.seconds_to_pretty_time($challenge['min_seconds_between_submissions']).' between submissions.' : ''),'
                        ',number_format($remaining_submissions),' submissions remaining. Available for another ', time_remaining($challenge['available_until']),'.
                    </p>
                    <button class="btn btn-sm btn-primary" type="submit">Submit flag</button>
                </form>
            </div>
            ';
        }
        // no remaining submission attempts
        else {
            message_inline_red("You have no remaining submission attempts. If you've made an erroneous submission, please contact the organizers.");
        }
    }

    echo '
    </div> <!-- / challenge-container -->';
}

foot();