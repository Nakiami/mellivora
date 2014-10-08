<?php

function cut_string ($string, $len) {
    return substr($string, 0, $len);
}

function short_description ($string, $len) {

    if (strlen($string) > $len) {
        $string = cut_string($string, $len);
        $string .= ' ...';
    }

    return $string;
}

function requested_file_name () {
    $pathinfo = pathinfo($_SERVER['SCRIPT_NAME']);
    return $pathinfo['filename'];
}

function max_file_upload_size () {
    return min(php_bytes(ini_get('post_max_size')), CONFIG_MAX_FILE_UPLOAD_SIZE);
}

function php_bytes($val) {
    $val = trim($val);
    $last = strtolower($val[strlen($val)-1]);
    switch($last) {
        // The 'G' modifier is available since PHP 5.1.0
        case 'g':
            $val *= 1024;
        case 'm':
            $val *= 1024;
        case 'k':
            $val *= 1024;
    }

    return $val;
}

function prefer_ssl() {
    if (CONFIG_SSL_COMPAT && (!isset($_SERVER['HTTPS']) || strtolower($_SERVER['HTTPS']) != 'on')) {
        redirect('https://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'], true);
    }
}

function generate_random_int($min = 0, $max = PHP_INT_MAX) {
    $factory = new RandomLib\Factory;
    $generator = $factory->getMediumStrengthGenerator();

    $generator->generateInt($min, $max);
}

function generate_random_string($length, $alphabet = null) {
    $factory = new RandomLib\Factory;
    $generator = $factory->getMediumStrengthGenerator();

    if (empty($alphabet)) {
        return $generator->generateString($length);
    } else {
        return $generator->generateString($length, $alphabet);
    }
}

function get_ip($as_integer = false) {
    $ip = $_SERVER['REMOTE_ADDR'];

    if (CONFIG_TRUST_HTTP_X_FORWARDED_FOR_IP && isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        // in almost all cases, there will only be one IP in this header
        if (valid_ip($_SERVER['HTTP_X_FORWARDED_FOR'], true)) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        }
        // in the rare case where several IPs are listed
        else {
            $forwarded_for_list = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            foreach ($forwarded_for_list as $forwarded_for) {
                $forwarded_for = trim($forwarded_for);
                if (valid_ip($forwarded_for, true)) {
                    $ip = $forwarded_for;
                    break;
                }
            }
        }
    }

    if ($as_integer) {
        return inet_aton($ip);
    } else {
        return $ip;
    }
}

function inet_aton ($ip) {
    return sprintf('%u', ip2long($ip));
}

function inet_ntoa ($num) {
    return long2ip(sprintf('%d', $num));
}

function valid_ip($ip, $public_only = false) {
    // we only want public, non-reserved IPs
    if ($public_only) {
        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
            return true;
        } else {
            return false;
        }
    }

    // allow non-public and reserved IPs
    else {
        if (filter_var($ip, FILTER_VALIDATE_IP)) {
            return true;
        } else {
            return false;
        }
    }
}

function valid_id ($id) {
    if (isset($id) && is_numeric($id) && $id > 0) {
        return true;
    }

    return false;
}

function validate_id ($id) {
    if (!valid_id($id)) {

        if (CONFIG_LOG_VALIDATION_FAILURE_ID) {
            log_exception(new Exception('Invalid ID'));
        }

        message_error('Something went wrong.');
    }

    return true;
}

function log_exception (Exception $e) {
    db_insert(
        'exceptions',
        array(
            'added'=>time(),
            'added_by'=>(isset($_SESSION['id']) ? $_SESSION['id'] : 0),
            'message'=>$e->getMessage(),
            'code'=>$e->getCode(),
            'trace'=>$e->getTraceAsString(),
            'file'=>$e->getFile(),
            'line'=>$e->getLine(),
            'user_ip'=>get_ip(true),
            'user_agent'=>$_SERVER['HTTP_USER_AGENT'],
            'user_agent_full'=>print_r(get_browser(null, true), true)
        )
    );
}

function time_remaining ($until, $from = false) {

    if ($from===false) {
        $until = $until - time();
    } else {
        $until = $until - $from;
    }

    return seconds_to_pretty_time($until);
}

function time_elapsed ($to, $from = false) {

    if ($from===false) {
        $to = time() - $to;
    } else {
        $to = $to - $from;
    }

    return seconds_to_pretty_time($to);
}

function date_time($timestamp = false, $specific = 6) {

    if($timestamp === false) {
        $timestamp = time();
    }

    $specific = substr('Y-m-d H:i:s', 0, ($specific*2)-1);

    return date($specific, $timestamp);
}

function seconds_to_pretty_time ($seconds) {
    $time = new DateTime(date('Y-m-d H:i:s', $seconds));
    $start = new DateTime(date('Y-m-d H:i:s', 0));
    $diff = $time->diff($start);

    if ($diff->y) {
        $time_string = $diff->y . append_if_plural(' year', 's', $diff->y) . ($diff->m ? ', ' . $diff->m . append_if_plural(' month', 's', $diff->m)  : '');
    }

    else if ($diff->m) {
        $time_string = $diff->m . append_if_plural(' month', 's', $diff->m) . ($diff->d ? ', ' . $diff->d . append_if_plural(' day', 's', $diff->d) : '');
    }

    else if ($diff->d) {
        $time_string = $diff->d . append_if_plural(' day', 's', $diff->d) . ($diff->h ? ', ' . $diff->h . append_if_plural(' hour', 's', $diff->h) : '');
    }

    else if ($diff->h) {
        $time_string = $diff->h . append_if_plural(' hour', 's', $diff->h) . ($diff->i ? ', ' . $diff->i . append_if_plural(' minute', 's', $diff->i) : '');
    }

    else if ($diff->i) {
        $time_string = $diff->i . append_if_plural(' minute', 's', $diff->i) . ($diff->s ? ', ' . $diff->s . append_if_plural(' second', 's', $diff->s) : '');
    }

    else {
        $time_string = $diff->s . append_if_plural(' second', 's', $diff->s);
    }

    return ($seconds < 0 ? '-' : '') . $time_string;
}

function append_if_plural($string, $to_add, $val) {
    return $string . ($val > 1 ? $to_add : '');
}

function bytes_to_pretty_size($bytes) {
    if ($bytes < 1000 * 1024) {
        return number_format($bytes / 1024, 2) . ' KB';
    }
    else if ($bytes < 1000 * 1048576) {
        return number_format($bytes / 1048576, 2) . ' MB';
    }
    else if ($bytes < 1000 * 1073741824) {
        return number_format($bytes / 1073741824, 2) . ' GB';
    }
    else {
        return number_format($bytes / 1099511627776, 2) . ' TB';
    }
}

function delete_challenge_cascading ($id) {

    if(!valid_id($id)) {
        message_error('Invalid ID.');
    }

    try {
        db_begin_transaction();

        db_delete(
            'challenges',
            array(
                'id'=>$id
            )
        );

        db_delete(
            'submissions',
            array(
                'challenge'=>$id
            )
        );

        db_delete(
            'hints',
            array(
                'challenge'=>$id
            )
        );

        $files = db_select_all(
            'files',
            array('id'),
            array('challenge'=>$id)
        );

        foreach ($files as $file) {
            delete_file($file['id']);
        }

        db_end_transaction();

    } catch(PDOException $e) {
        db_rollback_transaction();
        log_exception($e);
    }
}

function delete_file ($id) {

    if(!valid_id($id)) {
        message_error('Invalid ID.');
    }

    db_delete(
        'files',
        array(
            'id'=>$id
        )
    );

    if (file_exists(CONFIG_PATH_FILE_UPLOAD . $id)) {
        unlink(CONFIG_PATH_FILE_UPLOAD . $id);
    }
}

function invalidate_cache ($id, $group = 'default') {
    $path = CONFIG_PATH_CACHE . 'cache_' . $group . '_' . $id;
    if (file_exists($path)) {
        unlink($path);
    }
}

function validate_captcha () {

    $captcha = new Captcha\Captcha();
    $captcha->setPublicKey(CONFIG_RECAPTCHA_PUBLIC_KEY);
    $captcha->setPrivateKey(CONFIG_RECAPTCHA_PRIVATE_KEY);

    $response = $captcha->check();
    if (!$response->isValid()) {
        message_error ("The reCAPTCHA wasn't entered correctly. Go back and try it again.");
    }
}

function redirect ($url, $absolute = false) {
    header('location: ' . ($absolute ? $url : CONFIG_SITE_URL . htmlspecialchars($url)));
    exit();
}

function check_server_configuration() {
    // check for DB and PHP time mismatch
    $dbInfo = db_query_fetch_one('SELECT UNIX_TIMESTAMP() AS timestamp');
    $time = time();
    $error = abs($time - $dbInfo['timestamp']);
    if ($error >= 5) {
        message_inline_red('Database and PHP times are out of sync.
        This will likely cause problems.
        DB time: '.date_time($dbInfo['timestamp']).', PHP time: '.date_time($time).' ('.$error.' seconds off).
        Maybe you have different time zones set?');
    }

    // check that our writable dirs are writable
    if (!is_writable(CONFIG_PATH_FILE_WRITABLE)) {
        message_inline_red('Writable directory does not exist, or your web server does not have write access to it.
        You will not be able to upload files or perform caching.');
    }

    if (version_compare(PHP_VERSION, '5.3.7', '<')) {
        message_inline_red('Your version of PHP is too old. You need at least 5.3.7. You are running: ' . PHP_VERSION);
    }
}

function file_upload_error_description($code) {
    switch ($code) {
        case UPLOAD_ERR_INI_SIZE:
            return 'The uploaded file exceeds the upload_max_filesize directive in php.ini';
        case UPLOAD_ERR_FORM_SIZE:
            return 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form';
        case UPLOAD_ERR_PARTIAL:
            return 'The uploaded file was only partially uploaded';
        case UPLOAD_ERR_NO_FILE:
            return 'No file was uploaded';
        case UPLOAD_ERR_NO_TMP_DIR:
            return 'Missing a temporary folder';
        case UPLOAD_ERR_CANT_WRITE:
            return 'Failed to write file to disk';
        case UPLOAD_ERR_EXTENSION:
            return 'File upload stopped by extension';
        default:
            return 'Unknown upload error';
    }
}

function get_pager_from($val) {
    if (isset($val['from']) && valid_id($val['from'])) {
        return $val['from'];
    }

    return 0;
}

function get_two_factor_auth_qr_url() {
    require_once(CONFIG_PATH_THIRDPARTY.'Google2FA/Google2FA.php');

    $user = db_query_fetch_one(
        'SELECT
            u.id,
            u.team_name,
            t.secret
        FROM users AS u
        JOIN two_factor_auth AS t
        WHERE
          u.id = :user_id',
        array(
            'user_id'=>$_SESSION['id']
        )
    );

    if (empty($user['id']) || empty($user['secret'])) {
        message_error('No two-factor authentication tokens found for this user.');
    }

    return Google2FA::get_qr_code_url($user['team_name'], $user['secret']);
}

function validate_two_factor_auth_code($code) {
    require_once(CONFIG_PATH_THIRDPARTY.'Google2FA/Google2FA.php');

    $valid = false;

    $secret = db_select_one(
        'two_factor_auth',
        array(
            'secret'
        ),
        array(
            'user_id'=>$_SESSION['id']
        )
    );

    try {
        $valid = Google2FA::verify_key($secret['secret'], $code);
    } catch (Exception $e) {
        message_error('Could not verify key.');
    }

    return $valid;
}

function generate_two_factor_auth_secret($length) {
    return generate_random_string($length, 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567');
}

function get_file_name($path) {
    return pathinfo(basename($path), PATHINFO_FILENAME);
}