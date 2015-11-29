<?php

function user_is_logged_in () {
    if (isset($_SESSION['id'])) {
        return $_SESSION['id'];
    }

    return false;
}

function user_is_enabled() {
    if ($_SESSION['enabled']) {
        return true;
    }

    return false;
}

function user_is_staff () {
    if (user_is_logged_in() && $_SESSION['class'] >= CONST_USER_CLASS_MODERATOR) {
        return true;
    }

    return false;
}

function user_class_name ($class) {
    switch ($class) {
        case CONST_USER_CLASS_MODERATOR:
            return lang_get('user_class_moderator');
        case CONST_USER_CLASS_USER:
            return lang_get('user_class_user');
    }

    log_exception(new Exception('User with unknown class: ' . $class));

    message_generic_error();
}

function login_session_refresh($force_user_data_reload = false) {
    // force a database reload of user data
    if (user_is_logged_in()) {

        update_user_last_active_time($_SESSION['id']);

        if ($force_user_data_reload) {

            $user = db_select_one(
                'users',
                array(
                    'id',
                    'class',
                    'enabled',
                    '2fa_status',
                    'download_key'
                ),
                array(
                    'id' => $_SESSION['id']
                )
            );

            if ($_SESSION['2fa_status'] == 'authenticated') {
                $user['2fa_status'] = $_SESSION['2fa_status'];
            }

            login_session_create($user);
        }
    }

    // if users session has expired, but they have the "remember me" cookie
    if (!user_is_logged_in() && login_cookie_isset()) {
        login_session_create_from_login_cookie();
    }

    if (user_is_logged_in() && !user_is_enabled()) {
        logout();
    }
}

function login_create($email, $password, $remember_me) {

    if(empty($email) || empty($password)) {
        message_error('Please enter your email and password.');
    }

    $user = db_select_one(
        'users',
        array(
            'id',
            'passhash',
            'download_key',
            'class',
            'enabled',
            '2fa_status'
        ),
        array(
            'email'=>$email
        )
    );

    if (!check_passhash($password, $user['passhash'])) {
        message_error('Login failed');
    }

    if (!$user['enabled']) {
        message_generic('Ooops!', 'Your account is not enabled.
        If you have just registered, this is normal - an email with instructions will be sent out closer to the event start date!
        In all other cases, please contact the system administrator with any questions.');
    }

    login_session_create($user);
    regenerate_tokens();

    if ($remember_me) {
        login_cookie_create($user);
    }

    log_user_ip($user['id']);

    return true;
}

function login_session_create($user) {
    $_SESSION['id'] = $user['id'];
    $_SESSION['class'] = $user['class'];
    $_SESSION['enabled'] = $user['enabled'];
    $_SESSION['2fa_status'] = $user['2fa_status'];
    $_SESSION['download_key'] = $user['download_key'];
    $_SESSION['fingerprint'] = get_fingerprint();

    update_user_last_active_time($user['id']);
}

function regenerate_tokens() {
    regenerate_xsrf_token();
    regenerate_submission_token();
}

function login_cookie_create($user, $token_series = false) {

    $time = time();
    $ip = get_ip(true);

    if (!$token_series) {
        $token_series = generate_random_string(16);
    }
    $token = generate_random_string(64);

    db_insert(
        'cookie_tokens',
        array(
            'added'=>$time,
            'ip_created'=>$ip,
            'ip_last'=>$ip,
            'user_id'=>$user['id'],
            'token_series'=>$token_series,
            'token'=>$token
        )
    );

    $cookie_content = array (
        't'=>$token,
        'ts'=>$token_series
    );

    setcookie(
        CONST_COOKIE_NAME, // name
        json_encode($cookie_content), // content
        $time+CONFIG_COOKIE_TIMEOUT, // expiry
        '/', // path
        null, // domain
        CONFIG_SSL_COMPAT, // serve over SSL only
        true // httpOnly
    );
}

function login_cookie_destroy() {

    if (!login_cookie_isset()) {
        return;
    }

    $cookie = login_cookie_decode();

    db_delete(
        'cookie_tokens',
        array(
            'token'=>$cookie['t'],
            'token_series'=>$cookie['ts']
        )
    );

    destroy_cookie(CONST_COOKIE_NAME);
}

function destroy_cookie($name) {
    unset($_COOKIE[$name]);

    setcookie(
        $name,
        '',
        time() - 3600,
        '/'
    );
}

function login_cookie_isset() {
    return isset($_COOKIE[CONST_COOKIE_NAME]);
}

function login_cookie_decode() {

    if (!login_cookie_isset()) {
        log_exception(new Exception('Tried to decode nonexistent login cookie'));
        logout();
    }

    $cookieObj = json_decode($_COOKIE[CONST_COOKIE_NAME]);

    return array('t'=>$cookieObj->{'t'}, 'ts'=>$cookieObj->{'ts'});
}

function login_session_create_from_login_cookie() {

    if (!login_cookie_isset()) {
        log_exception(new Exception('Tried to create session from nonexistent login cookie'));
        logout();
    }

    $cookie = login_cookie_decode();

    $cookie_token_entry = db_select_one(
        'cookie_tokens',
        array(
            'user_id'
        ),
        array(
            'token'=>$cookie['t'],
            'token_series'=>$cookie['ts']
        )
    );

    if (!$cookie_token_entry['user_id']) {

        /*
         * TODO, here we could check:
         *    - if the token_series matches but
         *    - the token does not match
         * this probably means someone has already
         * used this cookie to re-authenticate.
         * This probably mean the cookie has been stolen.
         */

        log_exception(new Exception('An invalid cookie token was used. Cookie likely stolen. TS: ' . $cookie['ts']));
        logout();

        // explicitly exit here, even
        // though we do in redirect()
        exit;
    }

    // get the user whom this token
    // was issued for
    $user = db_select_one(
        'users',
        array(
            'id',
            'class',
            'enabled',
            '2fa_status',
            'download_key'
        ),
        array(
            'id'=>$cookie_token_entry['user_id']
        )
    );

    // remove the cookie token from the db
    // as it is used, and we don't want it
    // to every be used again
    db_delete(
        'cookie_tokens',
        array(
            'token'=>$cookie['t'],
            'token_series'=>$cookie['ts']
        )
    );

    // issue a new login cookie for the user
    // using the same token series identifier
    login_cookie_create($user, $cookie['ts']);

    login_session_create($user);
    regenerate_tokens();
}

function update_user_last_active_time($user_id) {

    validate_id($user_id);

    $now = time();

    if (!array_get($_SESSION, 'last_active') || $now - $_SESSION['last_active'] > CONST_USER_MIN_SECONDS_BETWEEN_ACTIVITY_LOG) {

        db_update(
            'users',
            array('last_active' => $now),
            array('id' => $user_id)
        );

        $_SESSION['last_active'] = $now;
    }
}

function log_user_ip($user_id) {

    validate_id($user_id);

    $now = time();
    $ip = get_ip(true);

    $entry = db_select_one(
        'ip_log',
        array(
            'id',
            'times_used'
        ),
        array(
            'user_id'=>$user_id,
            'ip'=>$ip
        )
    );

    // if the user has logged in with this IP previously
    if ($entry['id']) {

        db_query_fetch_none('
            UPDATE ip_log SET
               last_used=UNIX_TIMESTAMP(),
               ip=:ip,
               times_used=times_used+1
            WHERE id=:id',
            array(
                'ip'=>$ip,
                'id'=>$entry['id']
            )
        );
    }
    // if this is a new IP
    else {
        db_insert(
            'ip_log',
            array(
                'added'=>$now,
                'last_used'=>$now,
                'user_id'=>$user_id,
                'ip'=>$ip
            )
        );
    }
}

function make_passhash($password) {
    $hash = password_hash($password, PASSWORD_DEFAULT);

    if (!$hash) {
        $error_message = 'Could not generate password hash. Do you have PHP '.CONST_MIN_REQUIRED_PHP_VERSION.'+ installed?';
        log_exception(new Exception($error_message));
        message_error($error_message);
    }

    return $hash;
}

function check_passhash($password, $hash) {
    return password_verify($password, $hash);
}

function get_fingerprint() {
    return md5(get_ip());
}

function get_user_download_key() {
    if (user_is_logged_in()) {
        if (!isset($_SESSION['download_key'])) {
            login_session_refresh(true);
        }
        return $_SESSION['download_key'];
    }
}

function login_session_destroy () {
    session_unset();
    session_destroy();
}

function enforce_authentication($min_class = CONST_USER_CLASS_USER, $force_user_data_reload = false) {
    login_session_refresh($force_user_data_reload);

    if (!user_is_logged_in()) {
        logout();
    }

    if ($_SESSION['class'] < $min_class) {
        log_exception(new Exception('Class less than required'));
        logout();
    }

    if (user_is_staff() && $_SESSION['fingerprint'] != get_fingerprint()) {
        logout();
    }

    enforce_2fa();
}

function enforce_2fa() {
    if ($_SESSION['2fa_status'] == 'enabled') {
        redirect('two_factor_auth');
    }
}

function session_set_2fa_authenticated() {
    $_SESSION['2fa_status'] = 'authenticated';
}

function logout() {
    login_session_destroy();
    login_cookie_destroy();
    redirect(CONFIG_INDEX_REDIRECT_TO);
}

function register_account($email, $password, $team_name, $country, $type = null) {

    if (!CONFIG_ACCOUNTS_SIGNUP_ALLOWED) {
        message_error(lang_get('registration_closed'));
    }

    if (empty($email) || empty($password) || empty($team_name)) {
        message_error(lang_get('please_fill_details_correctly'));
    }

    if (isset($type) && !is_valid_id($type)) {
        message_error(lang_get('invalid_team_type'));
    }

    if (strlen($team_name) > CONFIG_MAX_TEAM_NAME_LENGTH || strlen($team_name) < CONFIG_MIN_TEAM_NAME_LENGTH) {
        message_error('team_name_too_long_or_short');
    }

    validate_email($email);

    if (!allowed_email($email)) {
        message_error(lang_get('email_not_whitelisted'));
    }

    $num_countries = db_select_one(
        'countries',
        array('COUNT(*) AS num')
    );

    if (!isset($country) || !is_valid_id($country) || $country > $num_countries['num']) {
        message_error(lang_get('please_supply_country_code'));
    }

    $user = db_select_one(
        'users',
        array('id'),
        array(
            'team_name' => $team_name,
            'email' => $email
        ),
        null,
        'OR'
    );

    if ($user['id']) {
        message_error(lang_get('user_already_exists'));
    }

    $user_id = db_insert(
        'users',
        array(
            'email'=>$email,
            'passhash'=>make_passhash($password),
            'download_key'=>hash('sha256', generate_random_string(128)),
            'team_name'=>$team_name,
            'added'=>time(),
            'enabled'=>(CONFIG_ACCOUNTS_DEFAULT_ENABLED ? '1' : '0'),
            'user_type'=>(isset($type) ? $type : 0),
            'country_id'=>$country
        )
    );

    // insertion was successful
    if ($user_id) {

        // log signup IP
        log_user_ip($user_id);

        // signup email
        $email_subject = lang_get('signup_email_subject', array('site_name' => CONFIG_SITE_NAME));
        // body
        $email_body = lang_get(
            'signup_email_success',
            array(
                'team_name' => htmlspecialchars($team_name),
                'site_name' => CONFIG_SITE_NAME,
                'signup_email_availability' => CONFIG_ACCOUNTS_DEFAULT_ENABLED ?
                    lang_get('signup_email_account_availability_message_login_now') :
                    lang_get('signup_email_account_availability_message_login_later'),
                'signup_email_password' => CONFIG_ACCOUNTS_EMAIL_PASSWORD_ON_SIGNUP ?
                    lang_get('your_password_is') . ': ' . $password :
                    lang_get('your_password_was_set')
            )
        );

        // send details to user
        send_email(array($email), $email_subject, $email_body);

        // if account isn't enabled by default, display message and die
        if (!CONFIG_ACCOUNTS_DEFAULT_ENABLED) {
            message_generic(
                lang_get('signup_successful'),
                lang_get(
                    'signup_successful_text',
                    array('email' => htmlspecialchars($email))
                )
            );
        } else {
            return true;
        }
    }

    // no rows were inserted
    return false;
}