<?php

function is_user_logged_in () {
    if (isset($_SESSION['id'])) {
        return $_SESSION['id'];
    } else {
        return false;
    }
}

function is_staff () {
    if (is_user_logged_in() && $_SESSION['class'] >= CONFIG_UC_MODERATOR) {
        return true;
    } else {
        return false;
    }
}

function login_session_refresh() {

    if (!is_user_logged_in()) {
        logout();
    }

    if ($_SESSION['fingerprint'] != get_fingerprint()) {
        logout();
    }

    session_regenerate_id(true);
}

function login_session_create($postData) {

    global $db;

    $email = $postData[md5(CONFIG_SITE_NAME.'USR')];
    $password = $postData[md5(CONFIG_SITE_NAME.'PWD')];

    if(empty($email) || empty($password)) {
        stderr('Sorry', 'Please enter your email and password.');
    }

    $stmt = $db->prepare('SELECT id, passhash, salt, class, enabled FROM users WHERE email = :email');
    $stmt->execute(array(':email' => $email));
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!check_passhash($user['passhash'], $user['salt'], $password)) {
        message_error('Login failed');
    }

    if (!$user['enabled']) {
        message_generic('Ooops!', 'Your account is not enabled.
        If you have just registered, this is normal - an email with instructions will be sent out closer to the event start date!
        In all other cases, please contact the system administrator with any questions.');
    }

    log_user_ip($user['id']);
    session_variable_create($user);

    return true;
}

function log_user_ip($userId) {
    global $db;

    if (!$userId) {
        message_error('No user ID was supplied to the IP logging function');
    }

    $stmt = $db->prepare('SELECT id, times_used FROM ip_log WHERE user_id=:user_id AND ip=:ip');
    $stmt->execute(array(':user_id' => $userId, ':ip'=>get_ip(true)));
    $entry = $stmt->fetch(PDO::FETCH_ASSOC);

    $time = time();

    // if the user has logged in with this IP previously
    if ($entry['id']) {
        $stmt = $db->prepare('
        UPDATE ip_log SET
        last_used=UNIX_TIMESTAMP(),
        ip=:ip,
        times_used=times_used+1
        WHERE id=:id
        ');
        $stmt->execute(array(
            ':ip'=>get_ip(true),
            ':id'=>$entry['id']
        ));
    }
    // if this is a new IP
    else {
        db_insert(
            'ip_log',
            array(
                'added'=>$time,
                'last_used'=>$time,
                'user_id'=>$userId,
                'ip'=>get_ip(true)
            )
        );
    }
}

function check_passhash($hash, $salt, $password) {
    if ($hash == make_passhash($password, $salt)) {
        return true;
    }
    else {
        return false;
    }
}

function make_passhash($password, $salt) {
    return hash('sha256', $salt . $password . $salt . CONFIG_HASH_SALT);
}

function make_salt() {
    return hash('sha256', generate_random_string());
}

function session_variable_create ($user) {
    $_SESSION['id'] = $user['id'];
    $_SESSION['class'] = $user['class'];
    $_SESSION['enabled'] = $user['enabled'];
    $_SESSION['fingerprint'] = get_fingerprint();
}

function get_fingerprint() {
    return md5(get_ip());
}

function session_variable_destroy () {
    session_unset();
    session_destroy();
}

function enforce_authentication($minClass = CONFIG_UC_USER) {
    login_session_refresh();

    if ($_SESSION['class'] < $minClass) {
       log_exception(new Exception('Class less than required'));
       logout();
    }
}

function logout() {
    session_variable_destroy();
    header('location: '.CONFIG_INDEX_REDIRECT_TO);
    exit();
}

function register_account($postData) {
    global $db;

    if (!CONFIG_ACCOUNTS_SIGNUP_ALLOWED) {
        message_error('Registration is currently closed.');
    }

    $email = $postData[md5(CONFIG_SITE_NAME.'USR')];
    $password = $postData[md5(CONFIG_SITE_NAME.'PWD')];
    $team_name = $postData[md5(CONFIG_SITE_NAME.'TEAM')];

    if (empty($email) || empty($password) || empty($team_name) || empty($postData['type'])) {
        message_error('Please fill in all the details correctly.');
    }

    if (strlen($team_name) > CONFIG_MAX_TEAM_NAME_LENGTH || strlen($team_name) < CONFIG_MIN_TEAM_NAME_LENGTH) {
        message_error('Your team name was too long or too short.');
    }

    validate_email($email);

    if (!pass_email_whitelist($email)) {
        message_error('Email not on whitelist. Please choose a whitelisted email or contact organizers.');
    }

    $stmt = $db->prepare('SELECT id FROM users WHERE team_name=:team_name OR email=:email');
    $stmt->execute(array(':team_name' => $team_name, ':email' => $email));
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user['id']) {
        message_error('An account with this team name or email already exists.');
    }

    $salt = make_salt();
    $user_id = db_insert(
        'users',
        array(
            'email'=>$email,
            'passhash'=>make_passhash($password, $salt),
            'salt'=>$salt,
            'team_name'=>$team_name,
            'added'=>time(),
            'enabled'=>(CONFIG_ACCOUNTS_DEFAULT_ENABLED ? '1' : '0'),
            'type'=>$postData['type']
        )
    );

    // insertion was successful
    if ($user_id) {

        // log signup IP
        log_user_ip($user_id);

        // signup email
        $email_subject = 'Signup successful - account details';
        // body
        $email_body = $team_name.', your registration at '.CONFIG_SITE_NAME.' was successful.'.
        "\r\n".
        "\r\n".
        'Your username is: '.$email.
        "\r\n".
        'Your password is: ';

        $email_body .= (CONFIG_ACCOUNTS_EMAIL_PASSWORD_ON_SIGNUP ? $password : '(encrypted)');

        $email_body .=
        "\r\n".
        "\r\n".
        'Please stay tuned for updates!'.
        "\r\n".
        "\r\n".
        'Regards,'.
        "\r\n".
        CONFIG_SITE_NAME.
        "\r\n".
        CONFIG_SITE_URL;

        // send details to user
        send_email($email, $team_name, $email_subject, $email_body);

        // if account isn't enabled by default, display message and die
        if (!CONFIG_ACCOUNTS_DEFAULT_ENABLED) {
            message_generic('Signup successful', 'Thank you for registering!
            Your chosen email is: ' . htmlspecialchars($email) . '.
            Please stay tuned for updates!');
        }
        else {
            return true;
        }
    }
    // no rows were inserted
    else {
        return false;
    }
}
