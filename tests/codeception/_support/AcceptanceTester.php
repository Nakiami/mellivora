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

    function log_in($email, $password) {
        $I = $this;

        $I->amOnPage('/scores');
        $I->click(['link'=>'Log in']);
        $I->amOnPage('/scores'); # I remain on this page after bringing down the login dialog

        $I->fillField('#login-email-input', $email);
        $I->fillField('#login-password-input', $password);
        $I->click('#login-button');

        $I->see('Log out'); # I am logged in
        $I->amOnPage('/scores'); # I have been redirected back to where I started
    }

    function register($email, $password) {
        $I = $this;

        $I->amOnPage('/home');

        $I->click(['link'=>'Register']);
        $I->amOnPage('/register');

        $I->fillField('team_name', 'testTeam');
        $I->fillField('#register-email-input', $email);
        $I->fillField('#register-password-input', $password);
        $I->selectOption('country', 'Afghanistan');
        $I->click('#register-team-button');

        $I->seeInDatabase('users', array('email' => $email));
    }
}
