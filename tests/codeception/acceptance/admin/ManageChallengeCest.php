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
        $I->amOnCategory(CI_DEFAULT_CATEGORY_TITLE);
        $I->see($title);
        $I->amOnPage('/scores');
        $I->see($title);
    }

    public function whenSettingAChallengeToBeNotExposedItShouldNotBeVisible(AcceptanceTester $I) {
        $I->logInAsAnAdmin();
        $I->amOnEditChallenge(CI_EDITABLE_CHALLENGE_ID);

        $title = time().'title';
        $description = time().'body';
        $from = date_time();
        $until = date_time(time() + 10000);

        $I->fillField('title', $title);
        $I->fillField('description', $description);
        $I->uncheckOption('#exposed');
        $I->fillField('available_from', $from);
        $I->fillField('available_until', $until);
        $I->selectOption('form select[name=category]', CI_DEFAULT_CATEGORY_TITLE);
        $I->click('Save changes');

        $I->waitForText('Edit challenge');
        $I->seeInCurrentUrl('/edit_challenge');
        $I->seeInField('title', $title);
        $I->seeInField('description', $description);
        $I->dontSeeCheckboxIsChecked('#exposed');
        $I->seeInField('available_from', $from);
        $I->seeInField('available_until', $until);

        $I->amOnAdminHome();
        $I->see($title);
        $I->amOnCategory(CI_DEFAULT_CATEGORY_TITLE);
        $I->dontSee($title);
        $I->amOnPage('/scores');
        $I->dontSee($title);
    }

    public function whenSettingAChallengeToBeExposedAtADateInTheFutureItShouldNotBeVisible(AcceptanceTester $I) {
        $I->logInAsAnAdmin();
        $I->amOnEditChallenge(CI_EDITABLE_CHALLENGE_ID);

        $title = time().'title';

        $I->fillField('title', $title);
        $I->checkOption('#exposed');
        $from = date_time(time() - 10000);
        $until = date_time(time() - 100);
        $I->fillField('available_from', $from);
        $I->fillField('available_until', $until);
        $I->selectOption('form select[name=category]', CI_DEFAULT_CATEGORY_TITLE);
        $I->click('Save changes');

        $I->waitForText('Edit challenge');
        $I->seeCheckboxIsChecked('#exposed');
        $I->seeInField('available_from', $from);
        $I->seeInField('available_until', $until);

        $I->amOnCategory(CI_DEFAULT_CATEGORY_TITLE);
        $I->see($title);
        $I->dontSee('Please enter flag for challenge: ' . $title);

        $I->amOnAdminHome();
        $I->see($title);
        $I->amOnPage('/scores');
        $I->see($title);
    }

    public function shouldGetAnErrorWhenTryingToDeleteAChallengeWithoutTickingTheConfirmationBox(AcceptanceTester $I) {
        $I->logInAsAnAdmin();

        $I->makeChallengeAvailable(CI_EDITABLE_CHALLENGE_ID);

        $I->amOnEditChallenge(CI_EDITABLE_CHALLENGE_ID);
        $title = $I->grabValueFrom('title');

        $I->amOnAdminHome();
        $I->see($title);
        $I->amOnCategory(CI_DEFAULT_CATEGORY_TITLE);
        $I->see($title);
        $I->amOnPage('/scores');
        $I->see($title);

        $I->amOnEditChallenge(CI_EDITABLE_CHALLENGE_ID);
        $I->click('Delete challenge');

        $I->see('Error');
        $I->see('Please confirm delete');

        $I->amOnAdminHome();
        $I->see($title);
        $I->amOnCategory(CI_DEFAULT_CATEGORY_TITLE);
        $I->see($title);
        $I->amOnPage('/scores');
        $I->see($title);
    }

    /**
     * @depends shouldGetAnErrorWhenTryingToDeleteAChallengeWithoutTickingTheConfirmationBox
     */
    public function shouldDeleteAChallengeWhenTheConfirmationBoxIsTicked(AcceptanceTester $I) {
        $I->logInAsAnAdmin();

        $I->makeChallengeAvailable(CI_EDITABLE_CHALLENGE_ID);

        $I->amOnEditChallenge(CI_EDITABLE_CHALLENGE_ID);
        $title = $I->grabValueFrom('title');

        $I->amOnAdminHome();
        $I->see($title);
        $I->amOnCategory(CI_DEFAULT_CATEGORY_TITLE);
        $I->see($title);
        $I->amOnPage('/scores');
        $I->see($title);

        $I->amOnEditChallenge(CI_EDITABLE_CHALLENGE_ID);
        $I->checkOption('#delete_confirmation');
        $I->click('Delete challenge');

        $I->amOnAdminHome();
        $I->dontSee($title);
        $I->amOnCategory(CI_DEFAULT_CATEGORY_TITLE);
        $I->dontSee($title);
        $I->amOnPage('/scores');
        $I->dontSee($title);

        $I->amOnEditChallenge(CI_EDITABLE_CHALLENGE_ID);
        $I->see('Error');
        $I->see('No challenge found with this ID');
    }
}
