<?php
$I = new AcceptanceTester($scenario);
$I->amOnPage('/home');
$I->see('Powered by Mellivora');

$I->click(['link'=>'Register']);
$I->amOnPage('/register');

$new_team_email = time().'@'.time().'.com';

$I->fillField('team_name', 'testTeam');
$I->fillField('#login-email-input', $new_team_email);
$I->fillField('#login-password-input', 'password');
$I->selectOption('country', 'Afghanistan');
$I->click('#register-team-button');

$I->seeInDatabase('users', array('email' => $new_team_email));