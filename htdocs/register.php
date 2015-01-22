<?php

require('../include/mellivora.inc.php');

if (user_is_logged_in()) {
    redirect(CONFIG_LOGIN_REDIRECT_TO);
    exit();
}

prefer_ssl();

head('Register');

if (CONFIG_ACCOUNTS_SIGNUP_ALLOWED) {
    echo '
    <h2>Register your team</h2>
    <p>
        Your team shares one account.
        ',(CONFIG_ACCOUNTS_EMAIL_PASSWORD_ON_SIGNUP ? 'An confirmation email containing a random password will be sent to the chosen address.' : ''),'
    </p>
    <form method="post" id="registerForm" class="form-signin" action="actions/login">
        <input name="team_name" type="text" class="form-control" placeholder="Team name" minlength="',CONFIG_MIN_TEAM_NAME_LENGTH,'" maxlength="',CONFIG_MAX_TEAM_NAME_LENGTH,'" required />
        <input name="',md5(CONFIG_SITE_NAME.'USR'),'" type="email" class="form-control" placeholder="Email address" required />
        ',(!CONFIG_ACCOUNTS_EMAIL_PASSWORD_ON_SIGNUP ? '<input name="'.md5(CONFIG_SITE_NAME.'PWD').'" type="password" class="form-control" placeholder="Password" required />' : '');

    if (cache_start('register', CONFIG_CACHE_TIME_REGISTER)) {
        $user_types = db_select_all(
            'user_types',
            array(
                'id',
                'title',
                'description'
            )
        );

        if (!empty($user_types)) {
            echo '<select name="type" class="form-control">
            <option disabled selected>-- Please select team type --</option>';

            foreach ($user_types as $user_type) {
                echo '<option value="',htmlspecialchars($user_type['id']),'">',htmlspecialchars($user_type['title'] . ' - ' . $user_type['description']),'</option>';
            }

            echo '</select>';
        }

        country_select();
        cache_end('register');
    }

    if (CONFIG_RECAPTCHA_ENABLE_PUBLIC) {
        display_captcha();
    }

    echo '
    <input type="hidden" name="action" value="register" />
    <button class="btn btn-primary btn-lg" type="submit">Register team</button>
</form>
';

} else {
    message_inline_blue(
        'Sorry',
        'Registration is currently closed, but you can still <a href="interest">register your interest for upcoming events</a>.'
    );
}

foot();