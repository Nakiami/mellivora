<?php

/**
 * Attempts to prevent race conditions on form submissions by
 * creating and validating a new random token on form submit
 */

const CONST_SUBMISSION_TOKEN_KEY = 'form_submission_token';

function form_submission_token() {
    echo '<input type="hidden" name="',CONST_SUBMISSION_TOKEN_KEY,'" value="',htmlspecialchars($_SESSION[CONST_SUBMISSION_TOKEN_KEY]),'" />';
}

function validate_submission_token($token) {
    if ($token != $_SESSION[CONST_SUBMISSION_TOKEN_KEY]) {
        message_error('Submission token has expired, please resubmit form');
    }

    regenerate_submission_token();
}

function regenerate_submission_token() {
    $_SESSION[CONST_SUBMISSION_TOKEN_KEY] = generate_random_string(64);
}