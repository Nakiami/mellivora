<?php
class BasicAdminCest {

    public function shouldBeAbleToLogInAsAnAdmin(AcceptanceTester $I){
        $I->logInAsAnAdmin();
    }
}