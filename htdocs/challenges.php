<?php

require('../include/mellivora.inc.php');

enforce_authentication();

$now = time();

head('Challenges');

if (isset($_GET['status'])) {
    if ($_GET['status']=='correct') {
        message_dialog('Congratulations! You got the flag!', 'Correct flag', 'Yay!', 'challenge-attempt correct on-page-load');
    } else if ($_GET['status']=='incorrect') {
        message_dialog('Sorry! That wasn\'t correct', 'Incorrect flag', 'Ok', 'challenge-attempt incorrect on-page-load');
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
    array(
        'exposed'=>1
    ),
    'title ASC'
);

// determine which category to display
if (isset($_GET['category'])) {

    if (is_valid_id($_GET['category'])) {
        $current_category = array_search_matching_key(
            $_GET['category'],
            $categories,
            'id'
        );
    } else {
        $current_category = array_search_matching_key(
            $_GET['category'],
            $categories,
            'title',
            'to_permalink'
        );
    }

    if (!$current_category) {
        redirect('challenges');
    }

} else {
    // if no category is selected, display
    // the first available category
    foreach ($categories as $cat) {
        if ($now > $cat['available_from'] && $now < $cat['available_until']) {
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

if (empty($current_category)) {
    message_generic('Challenges', 'Your CTF is looking a bit empty! Start by adding a category using the management console.');
}

// write out our categories menu
echo '<div id="categories-menu">
<ul id="categories-menu">';
foreach ($categories as $cat) {
    if ($now < $cat['available_from'] || $now > $cat['available_until']) {
        echo '<li class="disabled">
        <a data-container="body" data-toggle="tooltip" data-placement="top" class="has-tooltip" title="Available in '.time_remaining($cat['available_from']).'.">',htmlspecialchars($cat['title']),'</a>
        </li>';
    } else {
        echo '<li ',($current_category['id'] == $cat['id'] ? ' class="active"' : ''),'><a href="',CONFIG_SITE_URL,'challenges?category=',htmlspecialchars(to_permalink($cat['title'])),'">',htmlspecialchars($cat['title']),'</a></li>';
    }
}
echo '</ul>
</div>';

// check that the category is actually available for display
if ($now < $current_category['available_from'] || $now > $current_category['available_until']) {
    message_generic('Category unavailable','This category is not available. It is open from ' . date_time($current_category['available_from']) . ' ('. time_remaining($current_category['available_from']) .' from now) until ' . date_time($current_category['available_until']) . ' ('. time_remaining($current_category['available_until']) .' from now)', false);
}

// write out the category description, if one exists
if ($current_category['description']) {
    echo '<div id="category-description">', get_bbcode()->parse($current_category['description']), '</div>';
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
       c.relies_on,
       IF(c.automark = 1, 0, (SELECT ss.id FROM submissions AS ss WHERE ss.challenge = c.id AND ss.user_id = :user_id_1 AND ss.marked = 0)) AS unmarked, -- a submission is waiting to be marked
       (SELECT ss.added FROM submissions AS ss WHERE ss.challenge = c.id AND ss.user_id = :user_id_2 AND ss.correct = 1) AS correct_submission_added, -- a correct submission has been made
       (SELECT COUNT(*) FROM submissions AS ss WHERE ss.challenge = c.id AND ss.user_id = :user_id_3) AS num_submissions, -- number of submissions made
       (SELECT max(ss.added) FROM submissions AS ss WHERE ss.challenge = c.id AND ss.user_id = :user_id_4) AS latest_submission_added
    FROM challenges AS c
    WHERE
       c.category = :category AND
       c.exposed = 1
    ORDER BY c.points ASC, c.id ASC',
    array(
        'user_id_1'=>$_SESSION['id'],
        'user_id_2'=>$_SESSION['id'],
        'user_id_3'=>$_SESSION['id'],
        'user_id_4'=>$_SESSION['id'],
        'category'=>$current_category['id']
    )
);

echo '<div id="challenges-container" class="panel-group">';
foreach($challenges as $challenge) {

    $has_remaining_submissions = has_remaining_submissions($challenge);

    // if the challenge isn't available yet, display a message and continue to next challenge
    if ($challenge['available_from'] > $now) {
        echo '
        <div class="panel panel-default challenge-container">
            <div class="panel-heading">
                <h4 class="challenge-head">Hidden challenge worth ', number_format($challenge['points']), 'pts</h4>
            </div>
            <div class="panel-body">
                <div class="challenge-description">
                    Available in ',time_remaining($challenge['available_from']),' (from ', date_time($challenge['available_from']), ' until ', date_time($challenge['available_until']), ')
                </div>
            </div>
        </div>';

        continue;
    }

    echo '
    <div class="panel ', get_submission_box_class($challenge, $has_remaining_submissions), ' challenge-container">
        <div class="panel-heading">
            <h4 class="challenge-head">
            <a href="challenge?id=',htmlspecialchars($challenge['id']),'">',htmlspecialchars($challenge['title']), '</a> (', number_format($challenge['points']), 'pts)';

            if ($challenge['correct_submission_added']) {
                $solve_position = db_query_fetch_one('
                    SELECT
                      COUNT(*)+1 AS pos
                    FROM
                      submissions AS s
                    WHERE
                      s.correct = 1 AND
                      s.added < :correct_submission_added AND
                      s.challenge = :challenge_id',
                    array(
                        'correct_submission_added'=>$challenge['correct_submission_added'],
                        'challenge_id'=>$challenge['id']
                    )
                );

                echo ' <span class="glyphicon glyphicon-ok"></span>';
                echo get_position_medal($solve_position['pos']);
            }

    echo '</h4>';

    if (should_print_metadata($challenge)) {
        print_time_left_tooltip($challenge);
    }

    echo '</div>

    <div class="panel-body">';

    unset($relies_on);
    // if this challenge relies on another being solved, get the related information
    if ($challenge['relies_on']) {
        $relies_on = db_query_fetch_one('
            SELECT
              c.id,
              c.title,
              cat.id AS category_id,
              cat.title AS category_title,
              s.correct AS has_solved_requirement
            FROM
              challenges AS c
            LEFT JOIN categories AS cat ON cat.id = c.category
            LEFT JOIN submissions AS s ON s.challenge = c.id AND s.correct = 1
            WHERE
              c.id = :relies_on',
            array('relies_on'=>$challenge['relies_on'])
        );
    }

    // if this challenge relies on another, and the user hasn't solved that requirement
    if (isset($relies_on) && !$relies_on['has_solved_requirement']) {
        echo '
            <div class="challenge-description relies-on">',
                lang_get(
                    'challenge_relies_on',
                    array(
                        'relies_on_link' => '<a href="challenge?id='.htmlspecialchars($relies_on['id']).'">'.htmlspecialchars($relies_on['title']).'</a>',
                        'relies_on_category_link' => '<a href="challenges?category='.htmlspecialchars($relies_on['category_id']).'">'.htmlspecialchars($relies_on['category_title']).'</a>'
                    )
                )
            ,'</div>
        ';
    }

    // this challenge either does not have a requirement, or has a requirement that has already been solved
    else {

        // write out challenge description
        if ($challenge['description']) {
            echo '
            <div class="challenge-description">
                ',get_bbcode()->parse($challenge['description']),'
            </div> <!-- / challenge-description -->';
        }

        // write out hints
        print_hints($challenge);

        // write out files
        print_challenge_files(get_challenge_files($challenge));

        // only show the hints and flag submission form if we're not already correct and if the challenge hasn't expired
        if (!$challenge['correct_submission_added'] && $challenge['available_until'] > $now) {

            // if we have already made a submission to a manually marked challenge
            if ($challenge['num_submissions'] && !$challenge['automark'] && $challenge['unmarked']) {
                message_inline_blue('Your submission is awaiting manual marking.');
            }

            // if we have remaining submissions, print the submission form
            else if ($has_remaining_submissions) {

                echo '
                <div class="challenge-submit">
                    <form method="post" class="form-flag" action="actions/challenges">
                        <textarea name="flag" id="flag-input-'.htmlspecialchars($challenge['id']).'" type="text" class="flag-input form-control" placeholder="Please enter flag for challenge: ',htmlspecialchars($challenge['title']),'"></textarea>
                        <input type="hidden" name="challenge" value="',htmlspecialchars($challenge['id']),'" />
                        <input type="hidden" name="action" value="submit_flag" />';

                form_xsrf_token();

                if (CONFIG_RECAPTCHA_ENABLE_PRIVATE) {
                    display_captcha();
                }

                echo '<button id="flag-submit-',htmlspecialchars($challenge['id']),'" class="btn btn-sm btn-primary flag-submit-button" type="submit" data-countdown="',max($challenge['latest_submission_added']+$challenge['min_seconds_between_submissions'], 0),'" data-countdown-done="Submit flag">Submit flag</button>';

                if (should_print_metadata($challenge)) {
                    echo '<div class="challenge-submit-metadata">';
                    print_submit_metadata($challenge);

                    echo '</div>';
                }

                echo '</form>';
                echo '
                </div>
                ';

            }
            // no remaining submission attempts
            else {
                message_inline_blue("You have no remaining submission attempts. If you've made an erroneous submission, please contact the organizers.");
            }
        }
    }

    echo '
    </div> <!-- / panel-body -->
    </div> <!-- / challenge-container -->';
}
echo '</div> <!-- / challenges-container-->';

foot();