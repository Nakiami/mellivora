<?php
$I = new AcceptanceTester($scenario);

$I->log_in(CI_ADMIN_EMAIL, CI_ADMIN_PASSWORD);

$I->see('Manage'); # I am an admin user