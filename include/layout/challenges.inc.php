<?php

function print_submit_interval($challenge) {
    echo ($challenge['min_seconds_between_submissions'] ? 'Minimum of '.seconds_to_pretty_time($challenge['min_seconds_between_submissions']).' between submissions.' : '');
}

function print_submissions_left($challenge) {
    $remaining_submissions = get_remaining_submisions($challenge);
    echo ($challenge['num_attempts_allowed'] ? ''.number_format($remaining_submissions).' submissions remaining.' : '');
}

function print_time_left($challenge) {
    echo '<span data-countdown="', $challenge['available_until'],'">';
    echo time_remaining($challenge['available_until']), ' remaining';
    echo '</span>';
}

function print_time_left_tooltip($challenge) {
    echo '<span class="glyphicon glyphicon-time"></span> ';
    print_time_left($challenge);
}

function should_print_metadata($challenge) {
    $remaining_submissions = get_remaining_submisions($challenge);
    return !$challenge['correct'] && time() < $challenge['available_until'] && $remaining_submissions;
}

function get_remaining_submisions($challenge) {
    return $challenge['num_attempts_allowed'] ? ($challenge['num_attempts_allowed']-$challenge['num_submissions']) : 1;
}

?>