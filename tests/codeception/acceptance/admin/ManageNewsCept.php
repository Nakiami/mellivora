<?php
$I = new AcceptanceTester($scenario);

$I->logInAsAnAdmin();

$I->click('Manage');
$I->seeInCurrentUrl('/admin');
$I->click('News');
$I->click('Add news item');

$I->seeInCurrentUrl('/new_news');
$title = time().'title';
$body = time().'body';
$I->fillField('title', $title);
$I->fillField('body', $body);
$I->click('Publish news item');

$I->seeInDatabase('news', array('title' => $title, 'body' => $body));