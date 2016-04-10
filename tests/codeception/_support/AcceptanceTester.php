<?php


/**
 * Inherited Methods
 * @method void wantToTest($text)
 * @method void wantTo($text)
 * @method void execute($callable)
 * @method void expectTo($prediction)
 * @method void expect($prediction)
 * @method void amGoingTo($argumentation)
 * @method void am($role)
 * @method void lookForwardTo($achieveValue)
 * @method void comment($description)
 * @method \Codeception\Lib\Friend haveFriend($name, $actorClass = NULL)
 *
 * @SuppressWarnings(PHPMD)
*/
class AcceptanceTester extends \Codeception\Actor
{
    use _generated\AcceptanceTesterActions;

    public function logInAsANormalUser($email = CI_COMPETITOR_EMAIL, $password = CI_COMPETITOR_PASSWORD) {
        $I = $this;

        $I->logIn($email, $password);
        $I->amNotAnAdmin();
    }

    public function logInAsAnAdmin($user = CI_ADMIN_EMAIL, $password = CI_ADMIN_PASSWORD) {
        $I = $this;

        $I->logIn($user, $password);
        $I->amAnAdmin();
    }

    private function logIn($email, $password) {
        $I = $this;

        $I->amOnPage('/scores');
        $I->click(['link'=>'Log in']);
        $I->seeInCurrentUrl('/scores'); # I remain on this page after bringing down the login dialog

        $I->waitForElementVisible('#login-email-input', 5);
        $I->fillField('#login-email-input', $email);

        $I->waitForElementVisible('#login-password-input', 5);
        // some hax to make selenium fill out the password properly. this login attempt will be stopped by the js validation
        $I->click('#login-button');
        $I->fillField('#login-password-input', $password);

        $I->click('#login-button');

        $I->waitForText('Log out', 5); # I am logged in
        $I->seeInCurrentUrl('/scores'); # I have been redirected back to where I started
    }

    public function register($email, $password) {
        $I = $this;

        $I->amOnPage('/home');

        $I->click(['link'=>'Register']);
        $I->waitForText('Register your team', 5);
        $I->seeInCurrentUrl('/register');

        $I->fillField('team_name', 'testTeam');
        $I->fillField('#register-email-input', $email);
        $I->fillField('#register-password-input', $password);
        $I->selectOption('country', 'Afghanistan');
        $I->click('#register-team-button');

        $I->seeInDatabase('users', array('email' => $email));
    }

    public function amOnAdminHome() {
        $I = $this;

        $I->click('Manage');
        $I->waitForElement('#menu-management', 5);
        $I->seeInCurrentUrl('/admin');
    }

    public function amOnListNews() {
        $I = $this;

        $I->amOnAdminHome();
        $I->click('News');
        $I->click('List news items');
        $I->seeInCurrentUrl('/list_news');
    }

    public function amOnEditCategory($id) {
        $I = $this;

        $I->amOnAdminHome();
        $I->amOnPage('/admin/edit_category?id=' . $id);
    }

    public function makeCategoryAvailable($id) {
        $I = $this;

        $I->amOnEditCategory($id);

        $exposed = $I->grabFromDatabase('categories', 'exposed', array('id' => $id));
        $available_from = strtotime($I->grabValueFrom('available_from'));
        $available_until = strtotime($I->grabValueFrom('available_until'));

        if (!$exposed || $available_from > time() || $available_until < time()) {
            $I->checkOption('#exposed');
            $from = date_time(time() - 10000);
            $until = date_time(time() + 10000);
            $I->fillField('available_from', $from);
            $I->fillField('available_until', $until);

            $I->click('Save changes');
        }
    }

    public function amAnAdmin() {
        $I = $this;

        $I->see('Manage');
    }

    public function amNotAnAdmin() {
        $I = $this;

        $I->dontSee('Manage');
    }

    public function getUrlParam($param_key) {
        $url = $this->grabFromCurrentUrl();

        $parts = parse_url($url);
        parse_str($parts[$param_key], $params);

        return $params[$param_key];
    }
}
