<?php

require('../include/mellivora.inc.php');

login_session_refresh();

header('Content-type: application/json');

if (!isset($_GET['view'])) {
    echo json_error('please request a view');
}

if ($_GET['view'] == 'scoreboard') {
    if (cache_start('scores_json', CONFIG_CACHE_TIME_SCORES)) {
        json_scoreboard();
        cache_end('scores_json');
    }
}

else {
    echo json_error('not a valid view');
}