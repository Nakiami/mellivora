<?php

if (!defined('IN_FILE')) {
    exit(); // TODO report error
}

function loginSessionRefresh() {

    if (!$_SESSION['id']) {
        logout();
    }

    if ($_SESSION['fingerprint'] != getFingerPrint()) {
        logout();
    }

    session_regenerate_id(true);
}

function loginSessionCreate($postData) {

    global $db;

    $email = $postData[md5(CONFIG_SITE_NAME.'USR')];
    $password = $postData[md5(CONFIG_SITE_NAME.'PWD')];

    if(empty($email) || empty($password)) {
        stderr('Sorry', 'Please enter your email and password.');
    }

    $stmt = $db->prepare('SELECT id, passhash, salt, class, enabled FROM users WHERE email = :email');
    $stmt->execute(array(':email' => $email));
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!checkPass($user['passhash'], $user['salt'], $password)) {
        errorMessage('Login failed');
    }

    if (!$user['enabled']) {
        errorMessage('Your account is no longer enabled. Please contact the system administrator with any questions.');
    }

    sessionVariableCreate($user);

    return true;
}

function checkPass($hash, $salt, $password) {
    if ($hash == makePassHash($password, $salt)) {
        return true;
    }
    else {
        return false;
    }
}

function makePassHash($password, $salt) {
    return hash('sha256', $salt . $password . $salt . CONFIG_HASH_SALT);
}

function makeSalt() {
    return hash('sha256', generateRandomString());
}

function sessionVariableCreate ($user) {
    $_SESSION['id'] = $user['id'];
    $_SESSION['class'] = $user['class'];
    $_SESSION['enabled'] = $user['enabled'];
    $_SESSION['fingerprint'] = getFingerPrint();
}

function getFingerPrint() {
    return md5(geIP());
}

function sessionVariableDestroy () {
    session_unset();
    session_destroy();
}

function enforceAuthentication($minClass = CONFIG_UC_USER) {
    loginSessionRefresh();

    if ($_SESSION['class'] < $minClass) {
        logout();
    }
}

function logout() {
    sessionVariableDestroy();
    header('location: '.CONFIG_INDEX_REDIRECT_TO);
    exit();
}

function registerAccount($postData) {
    global $db;

    $email = $postData[md5(CONFIG_SITE_NAME.'USR')];
    $password = $postData[md5(CONFIG_SITE_NAME.'PWD')];
    $team_name = $postData[md5(CONFIG_SITE_NAME.'TEAM')];

    if (empty($email) || empty($password) || empty($team_name)) {
        errorMessage('Please fill in all the details correctly.');
    }

    validateEmail($email);

    $stmt = $db->prepare('SELECT id FROM users WHERE team_name=:team_name OR email=:email');
    $stmt->execute(array(':team_name' => $team_name, ':email' => $email));
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user['id']) {
        errorMessage('An account with this team name or email already exists.');
    }

    $stmt = $db->prepare('
    INSERT INTO users (
    email,
    passhash,
    salt,
    team_name,
    added
    ) VALUES (
    :email,
    :passhash,
    :salt,
    :team_name,
    UNIX_TIMESTAMP()
    )
    ');

    $salt = makeSalt();
    $stmt->execute(array(
        ':email' => $email,
        ':salt' => $salt,
        ':passhash' => makePassHash($password, $salt),
        ':team_name' => $team_name
    ));

    if ($stmt->rowCount()) {
        return true;
    } else {
        return false;
    }
}

function validateEmail($email) {

    global $db;

    // check email syntax
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        errorMessage('That doesn\'t look like an email. Please go back and double check the form.');
    }

    // check email rules
    $allowedEmail = true;
    list($userPrefix, $userDomain) = explode('@', $email);

    $stmt = $db->query('SELECT rule, white FROM restrict_email WHERE enabled = 1 ORDER BY priority ASC');
    while($rule = $stmt->fetch(PDO::FETCH_ASSOC)) {
        list($rulePrefix, $ruleDomain) = explode('@', $rule['rule']);

        if ($userDomain == $ruleDomain || $ruleDomain == '*') {
            if ($userPrefix == $rulePrefix || $rulePrefix == '*') {
                if ($rule['white']) {
                    $allowedEmail = true;
                } else {
                    $allowedEmail = false;
                }
            }
        }
    }

    if (!$allowedEmail) {
        errorMessage('Email not on whitelist. Please choose a whitelisted email or contact organizers.');
    }
}