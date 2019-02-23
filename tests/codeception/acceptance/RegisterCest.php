<?php

class RegisterCest {

    public function shouldBeAbleToRegisterANewUserAndLogIn(AcceptanceTester $I) {
        $email = time().'@'.time().'.com';
        $password = 'password';

        $I->register($email, $password);
        $I->logInAsANormalUser($email, $password);
    }
}
