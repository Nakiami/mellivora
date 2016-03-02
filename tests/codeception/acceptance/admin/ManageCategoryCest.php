<?php

class ManageCategoryCest {

    public function createCategory(AcceptanceTester $I) {
        $I->logInAsAnAdmin();

        $I->amOnAdminHome();
        $I->click('Categories');
        $I->click('Add category');

        $I->seeInCurrentUrl('/new_category');

        $title = time().'title';
        $description = time().'body';
        $from = date_time();
        $until = date_time(time() + 10000);

        $I->fillField('title', $title);
        $I->fillField('description', $description);
        $I->uncheckOption('#exposed');
        $I->fillField('available_from', $from);
        $I->fillField('available_until', $until);
        $I->click('Create category');

        $I->seeInCurrentUrl('/edit_category');
        $I->seeInField('title', $title);
        $I->seeInField('description', $description);
        $I->dontSeeCheckboxIsChecked('#exposed');
        $I->seeInField('available_from', $from);
        $I->seeInField('available_until', $until);

        $I->amOnAdminHome();
        $I->see($title);
    }

    /**
     * @depends createCategory
     */
    public function editCategory(AcceptanceTester $I) {
        $I->logInAsAnAdmin();

        $I->amOnAdminHome();
        $I->click('Edit category');

        $I->seeInCurrentUrl('/edit_category');

        $title = time().'title';
        $description = time().'body';
        $from = date_time();
        $until = date_time(time() + 10000);

        $I->fillField('title', $title);
        $I->fillField('description', $description);
        $I->checkOption('#exposed');
        $I->fillField('available_from', $from);
        $I->fillField('available_until', $until);
        $I->click('Save changes');

        $I->seeInCurrentUrl('/edit_category');
        $I->seeInField('title', $title);
        $I->seeInField('description', $description);
        $I->seeCheckboxIsChecked('#exposed');
        $I->seeInField('available_from', $from);
        $I->seeInField('available_until', $until);

        $I->amOnAdminHome();
        $I->see($title);
    }

    /**
     * @depends editCategory
     */
    public function deleteCategoryNoTickConfirmation(AcceptanceTester $I) {
        $I->logInAsAnAdmin();

        $I->amOnAdminHome();
        $I->click('Edit category');

        $I->seeInCurrentUrl('/edit_category');
        $I->click('Delete category');

        $I->see('Error');
        $I->see('Please confirm delete');

        $I->amOnAdminHome();
        $I->see('Edit category');
    }

    /**
     * @depends editCategory
     */
    public function deleteCategoryTickConfirmation(AcceptanceTester $I) {
        $I->logInAsAnAdmin();

        $I->amOnAdminHome();
        $I->click('Edit category');

        $I->seeInCurrentUrl('/edit_category');
        $I->checkOption('#delete_confirmation');
        $I->click('Delete category');

        $I->seeInCurrentUrl('/admin');
        $I->dontSee('Edit category');
    }
}
