<?php

class SubmitFlagCest {
    public function submitIncorrect(AcceptanceTester $I) {
        $I->logInAsANormalUser();

        $I->amOnPage('/challenges?category=' . to_permalink(CI_DEFAULT_CATEGORY_TITLE));
        $I->see(CI_DEFAULT_CHALLENGE_TITLE);
        $I->see(CI_DEFAULT_CHALLENGE_DESCRIPTION);

        $flag_field = '#flag-input-' . CI_DEFAULT_CHALLENGE_ID;
        $I->fillField($flag_field, 'NOT_THE_FLAG');
        $I->click('#flag-submit-' . CI_DEFAULT_CHALLENGE_ID);

        $I->seeInCurrentUrl('status=incorrect');
        $I->seeElement($flag_field);
    }

    public function submitCorrect(AcceptanceTester $I) {
        $I->logInAsANormalUser();

        $I->amOnPage('/challenges?category=' . to_permalink(CI_DEFAULT_CATEGORY_TITLE));
        $I->see(CI_DEFAULT_CHALLENGE_TITLE);
        $I->see(CI_DEFAULT_CHALLENGE_DESCRIPTION);

        $flag_field = '#flag-input-' . CI_DEFAULT_CHALLENGE_ID;
        $I->fillField($flag_field, CI_DEFAULT_CHALLENGE_FLAG);
        $I->click('#flag-submit-' . CI_DEFAULT_CHALLENGE_ID);

        $I->seeInCurrentUrl('status=correct');
        $I->dontSeeElement($flag_field);
    }
}