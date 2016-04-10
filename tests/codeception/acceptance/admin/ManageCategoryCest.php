<?php

class ManageCategoryCest {

    public function createCategory(AcceptanceTester $I) {
        $I->logInAsAnAdmin();

        $I->amOnAdminHome();
        $I->click('Categories');
        $I->click('Add category');

        $I->waitForText('New category');
        $I->seeInCurrentUrl('/new_category');

        $title = time().'title';
        $description = time().'body';
        $from = date_time();
        $until = date_time(time() + 10000);

        $I->fillField('title', $title);
        $I->fillField('description', $description);
        $I->seeCheckboxIsChecked('#exposed');
        $I->fillField('available_from', $from);
        $I->fillField('available_until', $until);
        $I->click('Create category');

        $I->waitForText('Edit category');
        $I->seeInCurrentUrl('/edit_category');
        $I->seeInField('title', $title);
        $I->seeInField('description', $description);
        $I->seeCheckboxIsChecked('#exposed');
        $I->seeInField('available_from', $from);
        $I->seeInField('available_until', $until);

        $I->amOnPage('/challenges');
        $I->see($title);

        $I->amOnAdminHome();
        $I->see($title);
    }

    public function editCategoryNotExposed(AcceptanceTester $I) {
        $I->logInAsAnAdmin();
        $I->amOnEditCategory(CI_EDITABLE_CATEGORY_ID);

        $title = time().'title';
        $description = time().'body';
        $from = date_time();
        $until = date_time(time() + 10000);

        $I->fillField('title', $title);
        $I->fillField('description', $description);
        $I->uncheckOption('#exposed');
        $I->fillField('available_from', $from);
        $I->fillField('available_until', $until);
        $I->click('Save changes');

        $I->waitForText('Edit category');
        $I->seeInCurrentUrl('/edit_category');
        $I->seeInField('title', $title);
        $I->seeInField('description', $description);
        $I->dontSeeCheckboxIsChecked('#exposed');
        $I->seeInField('available_from', $from);
        $I->seeInField('available_until', $until);

        $I->amOnPage('/challenges');
        $I->dontSee($title);
    }

    public function editCategoryNotVisibleTime(AcceptanceTester $I) {
        $I->logInAsAnAdmin();
        $I->amOnEditCategory(CI_EDITABLE_CATEGORY_ID);

        $title = time().'title';

        $I->fillField('title', $title);
        $I->checkOption('#exposed');
        $from = date_time(time() - 10000);
        $until = date_time(time() - 100);
        $I->fillField('available_from', $from);
        $I->fillField('available_until', $until);
        $I->click('Save changes');

        $I->waitForText('Edit category');
        $I->seeCheckboxIsChecked('#exposed');
        $I->seeInField('available_from', $from);
        $I->seeInField('available_until', $until);

        $I->amOnPage('/challenges');
        $I->see($title);
        $I->amOnPage('/challenges?category=' . to_permalink($title));
        $I->see('Category unavailable');
        $I->see('This category is not available. It is open from ' . $from);
        $I->see('until ' . $until);

        $I->amOnAdminHome();
        $I->see($title);
    }

    public function deleteCategoryNoTickConfirmation(AcceptanceTester $I) {
        $I->logInAsAnAdmin();
        $I->amOnEditCategory(CI_EDITABLE_CATEGORY_ID);

        $I->click('Delete category');

        $I->see('Error');
        $I->see('Please confirm delete');
    }

    /**
     * @depends deleteCategoryNoTickConfirmation
     */
    public function deleteCategoryTickConfirmation(AcceptanceTester $I) {
        $I->logInAsAnAdmin();
        $I->amOnEditCategory(CI_EDITABLE_CATEGORY_ID);

        $I->checkOption('#delete_confirmation');
        $I->click('Delete category');

        $I->seeInCurrentUrl('/admin');
        $I->dontSee(CI_EDITABLE_CATEGORY_TITLE);

        $I->amOnEditCategory(CI_EDITABLE_CATEGORY_ID);
        $I->see('Error');
        $I->see('No category found with this ID');
    }
}
