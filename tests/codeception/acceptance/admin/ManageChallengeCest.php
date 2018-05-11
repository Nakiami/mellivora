<?php

class ManageChallengeCest {

    public function shouldBeAbleToCreateANewChallenge(AcceptanceTester $I) {
        $I->logInAsAnAdmin();

        $I->amOnAdminHome();
        $I->click('Challenges', '#menu-management');
        $I->click('Add challenge');

        $I->waitForText('New challenge');
        $I->seeInCurrentUrl('/new_challenge');

        $title = time().'title';
        $description = time().'body';
        $from = date_time();
        $until = date_time(time() + 10000);

        $I->fillField('title', $title);
        $I->fillField('description', $description);
        $I->seeCheckboxIsChecked('#exposed');
        $I->fillField('available_from', $from);
        $I->fillField('available_until', $until);
        $I->selectOption('form select[name=category]', CI_DEFAULT_CATEGORY_TITLE);
        $I->click('Create challenge');

        $I->waitForText('Edit challenge');
        $I->seeInCurrentUrl('/edit_challenge');
        $I->seeInField('title', $title);
        $I->seeInField('description', $description);
        $I->seeCheckboxIsChecked('#exposed');
        $I->seeInField('available_from', $from);
        $I->seeInField('available_until', $until);
        $I->seeOptionIsSelected('form select[name=category]', CI_DEFAULT_CATEGORY_TITLE);

        $I->amOnAdminHome();
        $I->see($title);
        $I->amOnPage('/challenges');
        $I->click(CI_DEFAULT_CATEGORY_TITLE);
        $I->see($title);
        $I->amOnPage('/scores');
        $I->see($title);
    }
}
