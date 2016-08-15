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
    <h2>',lang_get('register_your_team'),'</h2>
    <p>
        ',lang_get(
            'account_signup_information',
            array(
                'password_information' => (CONFIG_ACCOUNTS_EMAIL_PASSWORD_ON_SIGNUP ? lang_get('email_password_on_signup') : '')
            )
        ),'
    </p>
    <form method="post" id="registerForm" class="form-signin" action="actions/register">
        <input name="team_name" type="text" class="form-control" placeholder="Team name" minlength="',CONFIG_MIN_TEAM_NAME_LENGTH,'" maxlength="',CONFIG_MAX_TEAM_NAME_LENGTH,'" required />
        <input name="',md5(CONFIG_SITE_NAME.'USR'),'" type="email" class="form-control" placeholder="Email address" id="register-email-input" required />
        ',(!CONFIG_ACCOUNTS_EMAIL_PASSWORD_ON_SIGNUP ? '<input name="'.md5(CONFIG_SITE_NAME.'PWD').'" type="password" class="form-control" placeholder="Password" id="register-password-input" required />' : '');

    if (cache_start(CONST_CACHE_NAME_REGISTER, CONFIG_CACHE_TIME_REGISTER)) {
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
        cache_end(CONST_CACHE_NAME_REGISTER);
    }

    if (CONFIG_RECAPTCHA_ENABLE_PUBLIC) {
        display_captcha();
    }

    echo '
    <input type="hidden" name="action" value="register" />
    <button class="btn btn-primary btn-lg" type="submit" id="register-team-button">Register team</button>
</form>
';

} else {
    message_inline_blue(
        'Registration is currently closed, but you can still <a href="interest">register your interest for upcoming events</a>.',
        false
    );
}

foot();
