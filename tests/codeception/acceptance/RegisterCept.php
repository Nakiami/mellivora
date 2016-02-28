<?php
$I = new AcceptanceTester($scenario);

$email = time().'@'.time().'.com';
$password = 'password';

$I->register($email, $password);
$I->logInAsANormalUser($email, $password);