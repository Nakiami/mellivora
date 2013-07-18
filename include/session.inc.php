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

    $username = $postData[md5(CONFIG_SITE_NAME.'USR')];
    $password = $postData[md5(CONFIG_SITE_NAME.'PWD')];

    if(empty($username) || empty($password)) {
        stderr('Sorry', 'Please enter your username and password.');
    }

    $stmt = $db->prepare('SELECT id, passhash, salt, class, enabled FROM users WHERE username = :username');
    $stmt->execute(array(':username' => $username));
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

    $username = $postData[md5(CONFIG_SITE_NAME.'USR')];
    $password = $postData[md5(CONFIG_SITE_NAME.'PWD')];
    $team_name = $postData[md5(CONFIG_SITE_NAME.'TEAM')];

    if (empty($username) || empty($password) || empty($team_name)) {
        errorMessage('Please fill in all the details correctly.');
    }

    if (!filter_var($username, FILTER_VALIDATE_EMAIL)) {
        errorMessage('That doesn\'t look like an email. Please go back and double check the form.');
    }

    $stmt = $db->prepare('SELECT id FROM users WHERE team_name=:team_name OR username=:username');
    $stmt->execute(array(':team_name' => $team_name, ':username' => $username));
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user['id']) {
        errorMessage('An account with this team name or username already exists.');
    }

    $stmt = $db->prepare('
    INSERT INTO users (
    username,
    passhash,
    salt,
    team_name,
    added
    ) VALUES (
    :username,
    :passhash,
    :salt,
    :team_name,
    UNIX_TIMESTAMP()
    )
    ');

    $salt = makeSalt();
    $stmt->execute(array(
        ':username' => $username,
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