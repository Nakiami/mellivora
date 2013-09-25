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

function cutString ($string, $len) {
    return substr($string, 0, $len);
}

function shortDescription ($string, $len) {

    if (strlen($string) > $len) {
        $string = cutString($string, $len);
        $string .= ' ...';
    }

    return $string;
}

function urlsToLinks($s) {
    return preg_replace('@(https?://([-\w\.]+[-\w])+(:\d+)?(/([\w/_\.#-]*(\?\S+)?[^\.\s])?)?)@', '<a href="$1">$1</a>', $s);
}

function getTimeElapsed ($to, $since = false) {

    if ($since===false) {
        $to = time() - $to;
    } else {
        $to = $to - $since;
    }

    return secondsToPrettyTime($to);
}

function secondsToPrettyTime ($to) {
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

function getDateTime($timestamp = false, $specific = 6) {

    if($timestamp === false) {
        $timestamp = time();
    }

    $specific = substr('Y-m-d H:i:s', 0, ($specific*2)-1);

    return date($specific, $timestamp);
}

function getClassName ($class) {
    switch ($class) {
        case CONFIG_UC_MODERATOR:
            echo 'Moderator';
            break;
        case CONFIG_UC_USER:
            echo 'User';
            break;
    }
}

function getRequestedFileName () {
    $pathinfo = pathinfo($_SERVER['SCRIPT_NAME']);
    return $pathinfo['filename'];
}

function forceSSL() {
    if (CONFIG_SSL_COMPAT && (!isset($_SERVER['HTTPS']) || strtolower($_SERVER['HTTPS']) != 'on')) {
        header('location: https://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
        exit();
    }
}

function generateRandomString($length = 25, $extended = true) {
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

function getIP() {

    $ip = $_SERVER['REMOTE_ADDR'];

    if (isset($_SERVER['HTTP_VIA']))
    {
        $forwarded_for = (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) ? (string) $_SERVER['HTTP_X_FORWARDED_FOR'] : '';

        if ($forwarded_for != $ip)
        {
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
    return long2ip(ip2long($ip));
}

function isValidIP($ip) {
    if (filter_var($ip, FILTER_VALIDATE_IP)) {
        return true;
    } else {
        return false;
    }
}

function intCheck($value, $return = true, $report = true) {
    if($value) {
        if (is_array($value)) {
            foreach ($value as $val) intCheck ($val, false);
        }

        if(!isValidID($value)) {
            if($report) {
                // TODO error reporting
            }
            else {
                errorMessage('Invalid ID found in request. Error code: '.__LINE__);
            }
        }

        if($return) {
            return $value;
        }
    }
}

function isValidID ($id) {
    if (is_numeric($id) && $id > 0) {
        return true;
    }

    return false;
}

function mkSize($bytes) {
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

function getPHPBytes($val) {
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

function sendEmail ($receiver, $receiver_name, $subject, $body, $from_email = CONFIG_EMAIL_FROM_EMAIL, $from_name = CONFIG_EMAIL_FROM_NAME, $replyto_email = CONFIG_EMAIL_REPLYTO_EMAIL, $replyto_name = CONFIG_EMAIL_REPLYTO_NAME) {

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
            errorMessage('Could not send email! Please contact '.(CONFIG_EMAIL_REPLYTO_EMAIL ? CONFIG_EMAIL_REPLYTO_EMAIL : CONFIG_EMAIL_FROM_EMAIL).' with this information: ' . $mail->ErrorInfo);
        }

    } catch (phpmailerException $e) {
        errorMessage('Could not send email! Please contact '.(CONFIG_EMAIL_REPLYTO_EMAIL ? CONFIG_EMAIL_REPLYTO_EMAIL : CONFIG_EMAIL_FROM_EMAIL).' with this information: ' . $e->errorMessage());
    } catch (Exception $e) {
        errorMessage('Could not send email! Please contact '.(CONFIG_EMAIL_REPLYTO_EMAIL ? CONFIG_EMAIL_REPLYTO_EMAIL : CONFIG_EMAIL_FROM_EMAIL).' with this information: ' . $e->getMessage());
    }
}

function checkCaptcha ($postData) {
    require_once(CONFIG_ABS_PATH . 'include/recaptcha/recaptchalib.php');

    $resp = recaptcha_check_answer (CONFIG_RECAPTCHA_PRIVATE_KEY, getIP(), $postData["recaptcha_challenge_field"], $postData["recaptcha_response_field"]);

    if (!$resp->is_valid) {
        errorMessage ('The reCAPTCHA wasn\'t entered correctly. Go back and try it again.');
    }
}

function deleteChallengeCascading ($id) {
    global $db;

    if(!isValidID($_POST['id'])) {
        errorMessage('Invalid ID.');
    }
    
    $stmt = $db->prepare('DELETE FROM challenges WHERE id=:id');
    $stmt->execute(array(':id'=>$id));

    $stmt = $db->prepare('DELETE FROM submissions WHERE challenge=:id');
    $stmt->execute(array(':id'=>$id));

    $stmt = $db->prepare('DELETE FROM hints WHERE challenge=:id');
    $stmt->execute(array(':id'=>$id));

    $stmt = $db->prepare('SELECT id FROM files WHERE challenge=:id');
    $stmt->execute(array(':id'=>$id));
    while ($file = $stmt->fetch(PDO::FETCH_ASSOC)) {
        deleteFile($file['id']);
    }
}

function deleteFile ($id) {
    global $db;

    if(!isValidID($_POST['id'])) {
        errorMessage('Invalid ID.');
    }

    $stmt = $db->prepare('DELETE FROM files WHERE id=:id');
    $stmt->execute(array(':id'=>$id));

    unlink(CONFIG_FILE_UPLOAD_PATH . $id);
}