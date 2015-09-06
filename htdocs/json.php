<?php

require('../include/mellivora.inc.php');

login_session_refresh();

header('Content-type: application/json');

if (!isset($_GET['view'])) {
    echo json_error(lang_get('please_request_view'));
    exit;
}

if ($_GET['view'] == 'scoreboard') {
    if (cache_start(CONST_CACHE_NAME_SCORES_JSON, CONFIG_CACHE_TIME_SCORES)) {
        json_scoreboard(array_get($_GET, 'user_type'));
        cache_end(CONST_CACHE_NAME_SCORES_JSON);
    }
}

else {
    echo json_error(lang_get('please_request_view'));
    exit;
}