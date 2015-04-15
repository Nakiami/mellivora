<?php

const CONST_XSRF_TOKEN_KEY = 'xsrf_token';

function form_xsrf_token() {
    echo '<input type="hidden" name="',CONST_XSRF_TOKEN_KEY,'" value="',htmlspecialchars($_SESSION[CONST_XSRF_TOKEN_KEY]),'" />';
}

function validate_xsrf_token($token) {
    if ($token != $_SESSION[CONST_XSRF_TOKEN_KEY]) {
        log_exception(new Exception('Invalid XSRF token. Was: "' . $token.'". Wanted: "' . $_SESSION[CONST_XSRF_TOKEN_KEY].'"'));
        logout();
        exit();
    }
}

function regenerate_xsrf_token() {
    $_SESSION[CONST_XSRF_TOKEN_KEY] = generate_random_string(64);
}