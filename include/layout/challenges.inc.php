<?php

function print_submit_interval($challenge) {
    echo ($challenge['min_seconds_between_submissions'] ? '<span class="glyphicon glyphicon-calendar"></span> Minimum of '.seconds_to_pretty_time($challenge['min_seconds_between_submissions']).' between submissions. ' : '');
}

function print_submissions_left($challenge) {
    $remaining_submissions = get_remaining_submisions($challenge);
    echo ($challenge['num_attempts_allowed'] ? '<span class="glyphicon glyphicon-inbox"></span> '.number_format($remaining_submissions).' submissions remaining.' : '');
}

function print_time_left($challenge) {
    echo '<span data-countdown="', $challenge['available_until'],'">';
    echo time_remaining($challenge['available_until']), ' remaining';
    echo '</span>';
}

function print_time_left_tooltip($challenge) {
    echo ' <span class="glyphicon glyphicon-time"></span> ';
    print_time_left($challenge);
}

function print_submit_metadata($challenge) {
    echo '<div class="challenge-submit-metadata">';
    echo '<p>';
    print_submissions_left($challenge);
    echo '</p><p>';
    print_submit_interval($challenge);
    echo '</p>';
    echo '</div>';
}

function print_attachments($files) {
    echo '<p><div class="challenge-files">';
    echo '<span class="glyphicon glyphicon-paperclip"></span> ';

    $firstFile = true;

    foreach ($files as $file) {
        if ($firstFile) {
            $firstFile = false;
        } else {
            echo ', ';
        }

        echo '<span class="challenge-attachment">';
        echo '<a class="has-tooltip" data-toggle="tooltip" data-placement="right" title="', bytes_to_pretty_size($file['size']) ,'" href="download?id=',htmlspecialchars($file['id']),'">',htmlspecialchars($file['title']),'</a>';
        echo '</span>';
    }

    echo '</p></div> <!-- / challenge-files -->';
}

function should_print_metadata($challenge) {
    $remaining_submissions = get_remaining_submisions($challenge);
    return !$challenge['correct'] && time() < $challenge['available_until'] && $remaining_submissions;
}

function get_remaining_submisions($challenge) {
    return $challenge['num_attempts_allowed'] ? ($challenge['num_attempts_allowed']-$challenge['num_submissions']) : 1;
}

?>