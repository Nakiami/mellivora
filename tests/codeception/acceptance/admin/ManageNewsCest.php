<?php

class ManageNewsCest {

    public function createNews(AcceptanceTester $I) {
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
     * @depends createNews
     */
    public function editNews(AcceptanceTester $I) {
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
     * @depends editNews
     */
    public function deleteNewsNoTickConfirmation(AcceptanceTester $I) {
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
     * @depends deleteNewsNoTickConfirmation
     */
    public function deleteNewsTickConfirmation(AcceptanceTester $I) {
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
