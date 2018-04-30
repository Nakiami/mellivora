<?php

// Hard-coded configuration files are optional.
// By default all configuration options can be
// overridden using environment variables

require('Config.php');

require('config/config.default.inc.php');
require('config/db.default.inc.php');

if (is_file('config/config.inc.php')) {
    require('config/config.inc.php');
}

if (is_file('config/db.inc.php')) {
    require('config/db.inc.php');
}