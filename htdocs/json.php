<?php

require('../include/mellivora.inc.php');

login_session_refresh();

header('Content-type: application/json');

if (!isset($_GET['view'])) {
    echo json_error('please request a view');
    exit;
}

if ($_GET['view'] == 'scoreboard') {
    if (cache_start('scores_json', CONFIG_CACHE_TIME_SCORES)) {
        json_scoreboard(array_get($_GET, 'user_type'));
        cache_end('scores_json');
    }
}

else {
    echo json_error('not a valid view');
    exit;
}