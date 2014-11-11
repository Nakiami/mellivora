<?php

function print_submit_interval($challenge) {
    echo ($challenge['min_seconds_between_submissions'] ? '<span class="glyphicon glyphicon-calendar"></span> Minimum of '.seconds_to_pretty_time($challenge['min_seconds_between_submissions']).' between submissions. ' : '');
}

function print_submissions_left($challenge) {
    $remaining_submissions = get_remaining_submisions($challenge);
    echo ($challenge['num_attempts_allowed'] ? '<span class="glyphicon glyphicon-inbox"></span> '.number_format($remaining_submissions).' submissions remaining.' : '');
}

function print_time_left($challenge) {
    echo '<span data-countdown="', $challenge['available_until'],'">
    ',time_remaining($challenge['available_until']), ' remaining
    </span>';
}

function print_time_left_tooltip($challenge) {
    echo ' <span class="glyphicon glyphicon-time"></span> ';
    print_time_left($challenge);
}

function print_submit_metadata($challenge) {
    echo
    '<p>',
    print_submissions_left($challenge),
    '</p><p>',
    print_submit_interval($challenge),
    '</p>';
}

function print_attachments($files) {
    echo '<div class="challenge-files">';
    foreach ($files as $file) {
        echo '
        <p>
            <span class="glyphicon glyphicon-paperclip"></span>
            <span class="challenge-attachment">
            <a class="has-tooltip" data-toggle="tooltip" data-placement="right" title="', bytes_to_pretty_size($file['size']) ,'" href="download?id=',htmlspecialchars($file['id']),'">',htmlspecialchars($file['title']),'</a>
            </span>
        <p>
        ';
    }

    echo '</p></div> <!-- / challenge-files -->';
}

function should_print_metadata($challenge) {
    $remaining_submissions = get_remaining_submisions($challenge);
    return !$challenge['correct_submission_added'] && time() < $challenge['available_until'] && $remaining_submissions;
}

function get_remaining_submisions($challenge) {
    return $challenge['num_attempts_allowed'] ? ($challenge['num_attempts_allowed']-$challenge['num_submissions']) : 1;
}

?>