<?php

require('../include/mellivora.inc.php');
require(CONST_PATH_THIRDPARTY . 'nbbc/nbbc.php');

enforce_authentication();

$time = time();

$bbc = new BBCode();
$bbc->SetEnableSmileys(false);

head(lang_get('challenges'));

if (isset($_GET['status'])) {
    if ($_GET['status']=='correct') {
        message_inline_green('<h1>'.lang_get('correct_flag').'</h1>', false);
    } else if ($_GET['status']=='incorrect') {
        message_inline_red('<h1>'.lang_get('incorrect_flag').'</h1>', false);
    } else if ($_GET['status']=='manual') {
        message_inline_blue('<h1>'.lang_get('submission_awaiting_mark').'</h1>', false);
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

    validate_id($_GET['category']);

    $current_category = array_search_matching_key(
        $_GET['category'],
        $categories,
        'id'
    );

    if (!$current_category) {
        message_error(
            lang_get('no_category_with_id'),
            false
        );
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

if (empty($current_category)) {
    message_generic(
        lang_get('challenges'),
        lang_get('ctf_empty')
    );
}

// write out our categories menu
echo '<div id="categories-menu">
<ul id="categories-menu">';
foreach ($categories as $cat) {
    if ($time < $cat['available_from'] || $time > $cat['available_until']) {
        echo '<li class="disabled">
        <a data-container="body" data-toggle="tooltip" data-placement="top" class="has-tooltip" title="',lang_get('available_in'),' ',time_remaining($cat['available_from']),'.">',htmlspecialchars($cat['title']),'</a>
        </li>';
    } else {
        echo '<li ',($current_category['id'] == $cat['id'] ? ' class="active"' : ''),'><a href="',CONFIG_SITE_URL,'challenges?category=',htmlspecialchars($cat['id']),'">',htmlspecialchars($cat['title']),'</a></li>';
    }
}
echo '</ul>
</div>';

// check that the category is actually available for display
if ($time < $current_category['available_from'] || $time > $current_category['available_until']) {
    message_generic(
        lang_get('cat_unavailable'),
        lang_get(
            'cat_unavailable_explanation',
            array(
                'available_from' => date_time($current_category['available_from']),
                'available_from_time_remaining' => time_remaining($current_category['available_from']),
                'available_until' => date_time($current_category['available_until']),
                'available_until_time_remaining' => time_remaining($current_category['available_until'])
            )
        ),
        false
    );
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
       c.relies_on,
       IF(c.automark = 1, 0, (SELECT ss.id FROM submissions AS ss WHERE ss.challenge = c.id AND ss.user_id = :user_id_1 AND ss.marked = 0)) AS unmarked, -- a submission is waiting to be marked
       (SELECT ss.added FROM submissions AS ss WHERE ss.challenge = c.id AND ss.user_id = :user_id_2 AND ss.correct = 1) AS correct_submission_added, -- a correct submission has been made
       (SELECT COUNT(*) FROM submissions AS ss WHERE ss.challenge = c.id AND ss.user_id = :user_id_3) AS num_submissions, -- number of submissions made
       (SELECT max(ss.added) FROM submissions AS ss WHERE ss.challenge = c.id AND ss.user_id = :user_id_4) AS latest_submission_added
    FROM challenges AS c
    WHERE c.category = :category
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

    // if the challenge isn't available yet, display a message and continue to next challenge
    if ($time < $challenge['available_from']) {
        echo '
        <div class="panel panel-default challenge-container">
            <div class="panel-heading">
                <h4 class="challenge-head">',
                    lang_get(
                        'hidden_challenge_worth',
                        array('pts' => number_format($challenge['points']))
                    )
                ,'</h4>
            </div>
            <div class="panel-body">
                <div class="challenge-description">
                    ',lang_get(
                        'available_in',
                        array(
                            'available_in' => time_remaining($challenge['available_from']),
                            'from' => date_time($challenge['available_from']),
                            'to' => date_time($challenge['available_until'])
                        )
                    ),'
                </div>
            </div>
        </div>';

        continue;
    }

    $remaining_submissions = $challenge['num_attempts_allowed'] ? ($challenge['num_attempts_allowed']-$challenge['num_submissions']) : 1;
    $panel_class = "panel-default";

    if (!$remaining_submissions) {
        $panel_class = "panel-danger";
    } else if ($challenge['correct_submission_added']) {
        $panel_class = "panel-success";
    }

    echo '
    <div class="panel ', $panel_class, ' challenge-container">
        <div class="panel-heading">
            <h4 class="challenge-head">
            <a href="challenge?id=',htmlspecialchars($challenge['id']),'">',htmlspecialchars($challenge['title']), '</a> (', number_format($challenge['points']), lang_get('points_short'),')';

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
                    'chal_relies_on',
                    array(
                        'relies_on_link' => '<a href="challenge?id='.htmlspecialchars($relies_on['id']).'">'.htmlspecialchars($relies_on['title']).'</a>',
                        'relies_on_category_link' => '<a href="challenges?category='.htmlspecialchars($relies_on['category_id']).'">'.htmlspecialchars($relies_on['category_title']).'</a>'
                    )
                )
            ,'</div>
        ';
    }

    // this challenge either does not have a requirement, or the user has solved it
    else {

        // write out challenge description
        if ($challenge['description']) {
            echo '
            <div class="challenge-description">
                ',$bbc->parse($challenge['description']),'
            </div> <!-- / challenge-description -->';
        }

        // only show the hints and flag submission form if we're not already correct and if the challenge hasn't expired
        if (!$challenge['correct_submission_added'] && $time < $challenge['available_until']) {

            // write out hints
            if (cache_start(CONST_CACHE_NAME_CHALLENGE_HINTS . $challenge['id'], CONFIG_CACHE_TIME_HINTS)) {
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

                cache_end(CONST_CACHE_NAME_CHALLENGE_HINTS . $challenge['id']);
            }

            if ($remaining_submissions) {

                if ($challenge['num_submissions'] && !$challenge['automark'] && $challenge['marked']) {
                    message_inline_blue(lang_get('submission_awaiting_mark'));
                }

                // write out files
                if (cache_start(CONST_CACHE_NAME_FILES . $challenge['id'], CONFIG_CACHE_TIME_FILES)) {
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
                        print_attachments($files);
                    }

                    cache_end(CONST_CACHE_NAME_FILES . $challenge['id']);
                }

                echo '
                <div class="challenge-submit">
                    <form method="post" class="form-flag" action="actions/challenges">
                        <textarea name="flag" type="text" class="flag-input form-control" placeholder="',lang_get('please_enter_flag'),' ',htmlspecialchars($challenge['title']),'"></textarea>
                        <input type="hidden" name="challenge" value="',htmlspecialchars($challenge['id']),'" />
                        <input type="hidden" name="action" value="submit_flag" />';

                form_xsrf_token();

                if (CONFIG_RECAPTCHA_ENABLE_PRIVATE) {
                    display_captcha();
                }

                echo '<button class="btn btn-sm btn-primary flag-submit-button" type="submit" data-countdown="',max($challenge['latest_submission_added']+$challenge['min_seconds_between_submissions'], 0),'" data-countdown-done="Submit flag">',lang_get('submit_flag'),'</button>';

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
                message_inline_red(lang_get('no_remaining_submissions'));
            }
        }
    }

    echo '
    </div> <!-- / panel-body -->
    </div> <!-- / challenge-container -->';
}
echo '</div> <!-- / challenges-container-->';

foot();