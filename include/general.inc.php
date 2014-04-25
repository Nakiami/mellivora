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

function urls_to_links($s) {
    return preg_replace('@(https?://([-\w\.]+[-\w])+(:\d+)?(/([\w/_\.#-]*(\?\S+)?[^\.\s])?)?)@', '<a href="$1">$1</a>', $s);
}

function get_domain_from_url($url) {
    preg_match('~^http[s]://(.+)/$~', $url, $output);

    if (!$output[1]) {
        message_error('Could not get domain name from URL. Is it in correct format? Should be http[s]://[something]/');
    }

    return $output[1];
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
        redirect('https://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
    }
}

function generate_random_string($length) {
    return substr(base64_encode(openssl_random_pseudo_bytes(100000)), 0, $length);
}

function get_ip($as_integer = false) {

    $ip = $_SERVER['REMOTE_ADDR'];

    if (isset($_SERVER['HTTP_VIA'])) {

        $forwarded_for = (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) ? (string) $_SERVER['HTTP_X_FORWARDED_FOR'] : '';

        if ($forwarded_for != $ip) {

            $ip = $forwarded_for;
            $nums = sscanf($ip, '%d.%d.%d.%d');
            if ($nums[0] === null ||
                $nums[1] === null ||
                $nums[2] === null ||
                $nums[3] === null ||
                $nums[0] == 10 ||
                ($nums[0] == 172 && $nums[1] >= 16 && $nums[1] <= 31) ||
                ($nums[0] == 192 && $nums[1] == 168) ||
                $nums[0] == 239 ||
                $nums[0] == 0 ||
                $nums[0] == 127)
                $ip = $_SERVER['REMOTE_ADDR'];
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

function valid_ip($ip) {
    if (filter_var($ip, FILTER_VALIDATE_IP)) {
        return true;
    }

    return false;
}

function valid_id ($id) {
    if (isset($id) && is_numeric($id) && $id > 0) {
        return true;
    }

    return false;
}

function validate_id ($id) {
    if (!valid_id($id)) {
        log_exception(new Exception('Invalid ID'));

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

function time_elapsed ($to, $since = false) {

    if ($since===false) {
        $to = time() - $to;
    } else {
        $to = $to - $since;
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

function seconds_to_pretty_time ($to) {
    $tokens = array (
        31536000 => 'year',
        2592000 => 'month',
        604800 => 'week',
        86400 => 'day',
        3600 => 'hour',
        60 => 'minute',
        1 => 'second'
    );

    foreach ($tokens as $unit => $text) {
        if ($to < $unit) continue;
        $numberOfUnits = floor($to / $unit);
        return $numberOfUnits.' '.$text.(($numberOfUnits>1)?'s':'');
    }
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
    $db = get_global_db_pdo();

    if(!valid_id($id)) {
        message_error('Invalid ID.');
    }

    try {
        $db->beginTransaction();

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

        $db->commit();

    } catch(PDOException $e) {
        $db->rollBack();
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

function check_captcha ($postData) {
    require_once(CONFIG_PATH_THIRDPARTY . 'recaptcha/recaptchalib.php');

    $resp = recaptcha_check_answer (
        CONFIG_RECAPTCHA_PRIVATE_KEY,
        get_ip(),
        $postData['recaptcha_challenge_field'],
        $postData['recaptcha_response_field']
    );

    if (!$resp->is_valid) {
        message_error ("The reCAPTCHA wasn't entered correctly. Go back and try it again.");
    }
}

function redirect ($url, $relative = false) {
    header('location: ' . ($relative ? '' : CONFIG_SITE_URL) . $url);
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

    // check that we have openssl
    if (!function_exists('openssl_random_pseudo_bytes')) {
        message_inline_red('PHP does not seem to have the OpenSSL library installed/enabled. This app will not function without it.');
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