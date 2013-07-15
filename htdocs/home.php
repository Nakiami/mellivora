<?php

define('IN_FILE', true);
require('../include/general.inc.php');

enforceAuthentication();

head('Login');

echo '<div class="page-header"><h2>Home</h2></div>';

echo 'Welcome to the Zombie apocalypse CTF!';

foot();