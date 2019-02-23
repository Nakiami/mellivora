<?php

class ManageNewsCest {

    public function shouldBeAbleToCreateANewNewsPost(AcceptanceTester $I) {
        $I->logInAsAnAdmin();

        $I->amOnAdminHome();
        $I->click('News');
        $I->click('Add news item');

        $I->waitForText('New news item');
        $I->seeInCurrentUrl('/new_news');

        $title = time().'title';
        $body = time().'body';

        $I->fillField('title', $title);
        $I->fillField('body', $body);
        $I->click('Publish news item');

        $I->seeInCurrentUrl('/edit_news');
        $I->seeInField('title', $title);
        $I->seeInField('body', $body);

        $I->amOnPage('/home');
        $I->see($title);
        $I->see($body);
    }

    /**
     * @depends shouldBeAbleToCreateANewNewsPost
     */
    public function shouldBeAbleToEditANewsPost(AcceptanceTester $I) {
        $I->logInAsAnAdmin();

        $I->amOnListNews();
        $I->click('Edit');

        $I->waitForText('Edit news item');
        $I->seeInCurrentUrl('/edit_news');

        $title = time().'title';
        $body = time().'body';

        $I->fillField('title', $title);
        $I->fillField('body', $body);
        $I->click('Save changes');

        $I->seeInCurrentUrl('/edit_news');
        $I->seeInField('title', $title);
        $I->seeInField('body', $body);

        $I->amOnPage('/home');
        $I->see($title);
        $I->see($body);
    }

    /**
     * @depends shouldBeAbleToEditANewsPost
     */
    public function shouldNotBeAbleToDeleteANewsPostWithoutTickingTheConfirmationBox(AcceptanceTester $I) {
        $I->logInAsAnAdmin();

        $I->amOnListNews();
        $I->click('Edit');

        $I->waitForText('Edit news item');
        $I->seeInCurrentUrl('/edit_news');
        $I->click('Delete news item');

        $I->see('Error');
        $I->see('Please confirm delete');

        $I->amOnListNews();
        $I->see('Edit');
    }

    /**
     * @depends shouldNotBeAbleToDeleteANewsPostWithoutTickingTheConfirmationBox
     */
    public function shouldBeAbleToDeleteANewsPostWhenTickingTheConfirmationBox(AcceptanceTester $I) {
        $I->logInAsAnAdmin();

        $I->amOnListNews();
        $I->click('Edit');

        $I->waitForText('Edit news item');
        $I->seeInCurrentUrl('/edit_news');
        $I->checkOption('#delete_confirmation');
        $I->click('Delete news item');

        $I->seeInCurrentUrl('/list_news');
        $I->dontSee('Edit');
    }
}
