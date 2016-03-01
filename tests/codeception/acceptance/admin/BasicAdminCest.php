<?php
class BasicAdminCest {

    public function login(AcceptanceTester $I){
        $I->logInAsAnAdmin();
    }
}