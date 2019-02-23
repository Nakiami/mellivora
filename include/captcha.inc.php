<?php

function display_captcha() {
    echo '
    <div class="g-recaptcha" data-sitekey="',Config::get('MELLIVORA_CONFIG_RECAPTCHA_PUBLIC_KEY'),'"></div>
    <script type="text/javascript" src="https://www.google.com/recaptcha/api.js?hl=en"></script>
    ';
}

function validate_captcha () {
    try {
        $captcha = new \ReCaptcha\ReCaptcha(
            Config::get('MELLIVORA_CONFIG_RECAPTCHA_PRIVATE_KEY'),
            new \ReCaptcha\RequestMethod\CurlPost()
        );

        $response = $captcha->verify(
            $_POST['g-recaptcha-response'],
            get_ip()
        );

        if (!$response->isSuccess()) {
            message_error('Captcha error');
        }

    } catch (Exception $e) {
        log_exception($e);
        message_error('Caught exception processing captcha. Please contact '.(Config::get('MELLIVORA_CONFIG_EMAIL_REPLYTO_EMAIL') ? Config::get('MELLIVORA_CONFIG_EMAIL_REPLYTO_EMAIL') : Config::get('MELLIVORA_CONFIG_EMAIL_FROM_EMAIL')));
    }
}