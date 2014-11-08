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
        if (is_valid_ip($_SERVER['HTTP_X_FORWARDED_FOR'], true)) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        }
        // in the rare case where several IPs are listed
        else {
            $forwarded_for_list = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            foreach ($forwarded_for_list as $forwarded_for) {
                $forwarded_for = trim($forwarded_for);
                if (is_valid_ip($forwarded_for, true)) {
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

function is_valid_ip($ip, $public_only = false) {
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

function is_valid_id ($id) {
    if (isset($id) && is_integer_value($id) && $id > 0) {
        return true;
    }

    return false;
}

function validate_id ($id) {
    if (!is_valid_id($id)) {

        if (CONFIG_LOG_VALIDATION_FAILURE_ID) {
            log_exception(new Exception('Invalid ID'));
        }

        message_error('Something went wrong.');
    }

    return true;
}

function validate_url ($url) {
    if (!filter_var($url, FILTER_VALIDATE_URL)) {
        log_exception(new Exception('Invalid URL in redirect: ' . $url));
        message_error('Invalid redirect URL. This has been reported.');
    }
}

function is_integer_value ($val) {
    return is_int($val) ? true : ctype_digit($val);
}

function log_exception (Exception $e) {
    db_insert(
        'exceptions',
        array(
            'added'=>time(),
            'added_by'=>array_get($_SESSION, 'id', 0),
            'message'=>$e->getMessage(),
            'code'=>$e->getCode(),
            'trace'=>$e->getTraceAsString(),
            'file'=>$e->getFile(),
            'line'=>$e->getLine(),
            'user_ip'=>get_ip(true),
            'user_agent'=>$_SERVER['HTTP_USER_AGENT']
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

    if(!is_valid_id($id)) {
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

    if(!is_valid_id($id)) {
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

function starts_with($haystack, $needle) {
    return $needle === '' || strpos($haystack, $needle) === 0;
}

function ends_with($haystack, $needle) {
    return $needle === '' || substr($haystack, -strlen($needle)) === $needle;
}

function redirect ($url, $absolute = false) {
    if (!$absolute) {
        $url = CONFIG_SITE_URL . trim($url, '/');
    }

    validate_url($url);

    header('location: ' . $url);
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

    if (version_compare(PHP_VERSION, CONST_MIN_REQUIRED_PHP_VERSION, '<')) {
        message_inline_red('Your version of PHP is too old. You need at least '.CONST_MIN_REQUIRED_PHP_VERSION.'. You are running: ' . PHP_VERSION);
    }
}

function visibility_enum_to_name ($visibility) {
    switch ($visibility) {
        case CONST_DYNAMIC_VISIBILITY_BOTH:
            return 'Both public and private';
        case CONST_DYNAMIC_VISIBILITY_PRIVATE:
            return 'Private';
        case CONST_DYNAMIC_VISIBILITY_PUBLIC:
            return 'Public';
    }

    return 'Unknown';
}

function get_pager_from($val) {
    if (is_valid_id(array_get($val, 'from'))) {
        return $val['from'];
    }

    return 0;
}

function array_get ($array, $key, $default = null) {
    return isset($array[$key]) ? $array[$key] : $default;
}