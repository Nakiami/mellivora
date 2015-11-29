<?php

function print_submit_interval($challenge) {
    echo ($challenge['min_seconds_between_submissions'] ? '<span class="glyphicon glyphicon-calendar"></span> '.lang_get(
            'minimum_time_between_submissions',
            array('time' => seconds_to_pretty_time($challenge['min_seconds_between_submissions']))
        ).' ' : '');
}

function print_submissions_left($challenge) {
    $remaining_submissions = get_remaining_submisions($challenge);
    echo ($challenge['num_attempts_allowed'] ?
        '<span class="glyphicon glyphicon-inbox"></span> '.lang_get(
            'num_submissions_remaining',
            array('num_remaining' => number_format($remaining_submissions))
        ) : '');
}

function print_time_left($challenge) {
    echo '<span data-countdown="', $challenge['available_until'],'">
    ',lang_get(
        'time_remaining',
        array('time' => time_remaining($challenge['available_until']))
    ), '
    </span>';
}

function print_time_left_tooltip($challenge) {
    echo ' <span class="glyphicon glyphicon-time"></span> ';
    print_time_left($challenge);
}

function print_submit_metadata($challenge) {
    echo '<p>';
    print_submissions_left($challenge);
    echo '</p><p>';
    print_submit_interval($challenge);
    echo '</p>';
}

function print_attachments($files) {
    echo '<div class="challenge-files">';
    foreach ($files as $file) {
        echo '
        <p>
            <div>
            <span class="glyphicon glyphicon-floppy-save"></span>
            <span class="challenge-attachment">
            <a class="has-tooltip" data-toggle="tooltip" data-placement="right" title="', bytes_to_pretty_size($file['size']) ,'" href="download?file_key=',htmlspecialchars($file['download_key']),'&amp;team_key=',get_user_download_key(),'">',htmlspecialchars($file['title']),'</a>
            ',($file['md5'] ? '<span class="has-tooltip" data-toggle="tooltip" data-placement="right" title="MD5 file hash"><pre class="inline-pre">'.$file['md5'].'</pre></span>' : ''),'
            </span>
            </div>
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
