<?php

class ManageNewsCest {

    public function createNews(AcceptanceTester $I) {
        $I->logInAsAnAdmin();

        $I->amOnAdminHome();
        $I->click('News');
        $I->click('Add news item');

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

        $I->amOnAdminHome();
        $I->click('News');
        $I->click('List news items');
        $I->click('Edit');

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
}
