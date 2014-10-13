<?php

function display_captcha() {
    echo '
        <script type="text/javascript">
         var RecaptchaOptions = {
                theme : "clean"
         };
         </script>
         ';

    $captcha = new Captcha\Captcha();
    $captcha->setPublicKey(CONFIG_RECAPTCHA_PUBLIC_KEY);
    $captcha->setPrivateKey(CONFIG_RECAPTCHA_PRIVATE_KEY);

    echo $captcha->html();
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