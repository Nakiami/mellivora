<?php

require('../include/mellivora.inc.php');

if (user_is_logged_in()) {
    redirect(CONFIG_LOGIN_REDIRECT_TO);
    exit();
}

force_ssl();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($_POST['action'] == 'login') {
        if (login_session_create($_POST)) {
            redirect(CONFIG_LOGIN_REDIRECT_TO);
        } else {
            message_error('Login failed? Helpful.');
        }
    }

    else if ($_POST['action'] == 'register') {

        if (CONFIG_RECAPTCHA_ENABLE) {
            check_captcha($_POST);
        }

        if (register_account($_POST) && login_session_create($_POST)) {
            redirect(CONFIG_REGISTER_REDIRECT_TO);
        } else {
            message_error('Sign up failed? Helpful.');
        }
    }

    exit();
}

head('Login');

echo '
<form method="post" class="form-signin">
    <h2>Please sign in</h2>
    <input name="',md5(CONFIG_SITE_NAME.'USR'),'" type="text" class="form-control" placeholder="Email address" required autofocus />
    <input name="',md5(CONFIG_SITE_NAME.'PWD'), '" type="password" class="form-control" placeholder="Password" required />
    <input type="hidden" name="action" value="login" />
    <button class="btn btn-primary" type="submit">Sign in</button> <a href="reset_password">I\'ve forgotten my password</a>
</form>
';

if (CONFIG_ACCOUNTS_SIGNUP_ALLOWED) {
    echo '
    <form method="post" class="form-signin">
        <h2>or, register a team</h2>
        <p>
            Your team shares one account.
            ',(CONFIG_ACCOUNTS_EMAIL_PASSWORD_ON_SIGNUP ? 'An confirmation email containing your password will be sent to the chosen address.' : ''),'
        </p>
        <input name="',md5(CONFIG_SITE_NAME.'USR'),'" type="text" class="form-control" placeholder="Email address" required />
        <input name="',md5(CONFIG_SITE_NAME.'PWD'),'" type="password" class="form-control" placeholder="Password" required />
        <input name="',md5(CONFIG_SITE_NAME.'TEAM'),'" type="text" class="form-control" placeholder="Team name" maxlength="',CONFIG_MAX_TEAM_NAME_LENGTH,'" required />

        <select name="type" class="form-control">
            <option>-- Please select team type. If your team is mixed, select the highest institution --</option>
            <option value="uni">University team</option>
            <option value="hs">High school team</option>
            <option value="tafe">TAFE team</option>
        </select>
        ';

        if (CONFIG_RECAPTCHA_ENABLE) {
            display_captcha();
        }

        echo '
        <input type="hidden" name="action" value="register" />
        <button class="btn btn-primary" type="submit">Register team</button>
    </form>
    ';
} else {
    echo '<i>Registration is currently closed, but you can still <a href="interest">register your interest for upcoming events</a>.</i>';
}

foot();