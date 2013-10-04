<?php

if (!defined('IN_FILE')) {
    exit(); // TODO report error
}

session_start();

require('config.inc.php');
require(CONFIG_ABS_PATH . 'include/session.inc.php');
require(CONFIG_ABS_PATH. 'include/graphics.inc.php');
require(CONFIG_ABS_PATH. 'include/Cache/Lite.php');

// connect to database
$db = new PDO(DB_ENGINE.':host='.DB_HOST.';dbname='.DB_NAME.';charset=utf8', DB_USER, DB_PASSWORD);
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

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

function get_time_elapsed ($to, $since = false) {

    if ($since===false) {
        $to = time() - $to;
    } else {
        $to = $to - $since;
    }

    return seconds_to_pretty_time($to);
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

function get_date_time($timestamp = false, $specific = 6) {

    if($timestamp === false) {
        $timestamp = time();
    }

    $specific = substr('Y-m-d H:i:s', 0, ($specific*2)-1);

    return date($specific, $timestamp);
}

function get_user_class_name ($class) {
    switch ($class) {
        case CONFIG_UC_MODERATOR:
            echo 'Moderator';
            break;
        case CONFIG_UC_USER:
            echo 'User';
            break;
    }
}

function get_requested_file_name () {
    $pathinfo = pathinfo($_SERVER['SCRIPT_NAME']);
    return $pathinfo['filename'];
}

function force_ssl() {
    if (CONFIG_SSL_COMPAT && (!isset($_SERVER['HTTPS']) || strtolower($_SERVER['HTTPS']) != 'on')) {
        header('location: https://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
        exit();
    }
}

function generate_random_string($length = 25, $extended = true) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

    if ($extended) {
        $characters .= '!@#$%&*()_+-={}[]:";';
    }

    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $randomString;
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

    $ip = long2ip(ip2long($ip));

    if ($as_integer) {
        return inet_aton($ip);
    } else {
        return $ip;
    }
}

function is_valid_ip($ip) {
    if (filter_var($ip, FILTER_VALIDATE_IP)) {
        return true;
    } else {
        return false;
    }
}

function int_check($value, $return = true, $report = true) {
    if($value) {
        if (is_array($value)) {
            foreach ($value as $val) int_check ($val, false);
        }

        if(!is_valid_id($value)) {
            if($report) {
                // TODO error reporting
            }
            else {
                message_error('Invalid ID found in request. Error code: '.__LINE__);
            }
        }

        if($return) {
            return $value;
        }
    }
}

function is_valid_id ($id) {
    if (isset($id) && is_numeric($id) && $id > 0) {
        return true;
    }

    return false;
}

function mk_size($bytes) {
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

function get_php_bytes($val) {
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

function send_email ($receiver, $receiver_name, $subject, $body, $from_email = CONFIG_EMAIL_FROM_EMAIL, $from_name = CONFIG_EMAIL_FROM_NAME, $replyto_email = CONFIG_EMAIL_REPLYTO_EMAIL, $replyto_name = CONFIG_EMAIL_REPLYTO_NAME) {

    require_once(CONFIG_ABS_PATH . 'include/PHPMailer/class.phpmailer.php');

    $mail = new PHPMailer();
    try {

        if (CONFIG_EMAIL_METHOD == 'smtp') {
            $mail->IsSMTP();

            $mail->SMTPDebug = CONFIG_EMAIL_SMTP_DEBUG_LEVEL;

            $mail->Host = CONFIG_EMAIL_SMTP_HOST;
            $mail->Port = CONFIG_EMAIL_SMTP_PORT;
            $mail->SMTPSecure = CONFIG_EMAIL_SMTP_SECURITY;

            $mail->SMTPAuth = CONFIG_EMAIL_SMTP_AUTH;
            $mail->Username = CONFIG_EMAIL_SMTP_USER;
            $mail->Password = CONFIG_EMAIL_SMTP_PASSWORD;
        }

        $mail->SetFrom($from_email, $from_name);
        if ($replyto_email) {
            $mail->AddReplyTo($replyto_email, $replyto_name);
        }
        $mail->AddAddress($receiver, $receiver_name);

        $mail->Subject = $subject;

        // HTML body
        //$mail->MsgHTML($body);
        $mail->Body = $body;

        //Send the message, check for errors
        if(!$mail->Send()) {
            message_error('Could not send email! Please contact '.(CONFIG_EMAIL_REPLYTO_EMAIL ? CONFIG_EMAIL_REPLYTO_EMAIL : CONFIG_EMAIL_FROM_EMAIL).' with this information: ' . $mail->ErrorInfo);
        }

    } catch (Exception $e) {
        log_exception($e);
        message_error('Could not send email! Please contact '.(CONFIG_EMAIL_REPLYTO_EMAIL ? CONFIG_EMAIL_REPLYTO_EMAIL : CONFIG_EMAIL_FROM_EMAIL));
    }
}

function check_captcha ($postData) {
    require_once(CONFIG_ABS_PATH . 'include/recaptcha/recaptchalib.php');

    $resp = recaptcha_check_answer (CONFIG_RECAPTCHA_PRIVATE_KEY, get_ip(), $postData["recaptcha_challenge_field"], $postData["recaptcha_response_field"]);

    if (!$resp->is_valid) {
        message_error ('The reCAPTCHA wasn\'t entered correctly. Go back and try it again.');
    }
}

function delete_challenge_cascading ($id) {
    global $db;

    if(!is_valid_id($_POST['id'])) {
        message_error('Invalid ID.');
    }

    try {
        $db->beginTransaction();
    
        $stmt = $db->prepare('DELETE FROM challenges WHERE id=:id');
        $stmt->execute(array(':id'=>$id));

        $stmt = $db->prepare('DELETE FROM submissions WHERE challenge=:id');
        $stmt->execute(array(':id'=>$id));

        $stmt = $db->prepare('DELETE FROM hints WHERE challenge=:id');
        $stmt->execute(array(':id'=>$id));

        $stmt = $db->prepare('SELECT id FROM files WHERE challenge=:id');
        $stmt->execute(array(':id'=>$id));
        while ($file = $stmt->fetch(PDO::FETCH_ASSOC)) {
            delete_file($file['id']);
        }

        $db->commit();

    } catch(PDOException $e) {
        $db->rollBack();
        log_exception($e);
    }
}

function delete_file ($id) {
    global $db;

    if(!is_valid_id($_POST['id'])) {
        message_error('Invalid ID.');
    }

    $stmt = $db->prepare('DELETE FROM files WHERE id=:id');
    $stmt->execute(array(':id'=>$id));

    unlink(CONFIG_FILE_UPLOAD_PATH . $id);
}

function validate_id ($id) {
   if (!is_valid_id($id)) {
      log_exception(new Exception('Invalid ID'));

      message_error('Something went wrong.');
   }

   return true;
}

function validate_email($email) {
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        log_exception(new Exception('Invalid Email'));

        message_error('That doesn\'t look like an email. Please go back and double check the form.');
    }
}

function check_email_whitelist ($email) {
    global $db;

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
        message_error('Email not on whitelist. Please choose a whitelisted email or contact organizers.');
    }
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

function db_update($table, array $fields, array $where, $whereGlue = 'AND') {
    global $db;

    try {
        $sql = 'UPDATE '.$table.' SET ';
        $sql .= implode('=?, ', array_keys($fields)).'=? ';
        $sql .= 'WHERE '.implode('=? '.$whereGlue.' ', array_keys($where)).'=?';

        $stmt = $db->prepare($sql);

        // get the field values and "WHERE" values. merge them into one array.
        $values = array_merge(array_values($fields), array_values($where));

        // execute the statement
        $stmt->execute($values);

    } catch (PDOException $e) {
        log_exception($e);
        return false;
    }

    return $stmt->rowCount();
}

function db_insert ($table, array $fields) {
   global $db;

    try {
        $sql = 'INSERT INTO '.$table.' (';
        $sql .= implode(', ', array_keys($fields));
        $sql .= ') VALUES (';
        $sql .= implode(', ', array_fill(0, count($fields), '?'));
        $sql .= ')';

        $stmt = $db->prepare($sql);

        // get the field values
        $values = array_values($fields);

        $stmt->execute($values);

    } catch (PDOException $e) {
        log_exception($e);
        return false;
    }

   return $db->lastInsertId();
}

function inet_aton ($ip) {
    return sprintf('%u', ip2long($ip));
}

function inet_ntoa ($num) {
    return long2ip(sprintf('%d', $num));
}