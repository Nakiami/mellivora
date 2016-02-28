<?php

function login_dialog() {
    echo '
    <div class="modal fade" id="login-dialog" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="login-dialog-form" method="post" class="form-signin" action="/actions/login">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                        <h4 class="modal-title" id="myModalLabel">',lang_get('log_in'),'</h4>
                    </div>
                    <div class="modal-body">
                            <input name="',md5(CONFIG_SITE_NAME.'USR'),'" type="email" class="form-control" placeholder="',lang_get('email_address'),'" id="login-email-input" required autofocus />
                            <input name="',md5(CONFIG_SITE_NAME.'PWD'), '" type="password" class="form-control" placeholder="',lang_get('password'),'" id="login-password-input" required />
                            <input type="hidden" name="action" value="login" />
                            <input type="hidden" name="redirect" value="',htmlspecialchars($_SERVER['REQUEST_URI']), '" />
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="remember_me" value="1" checked> ',lang_get('remember_me'),'
                                </label>
                            </div>
                            <a href="reset_password">',lang_get('forgotten_password'),'</a>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">',lang_get('close'),'</button>
                        <button type="submit" class="btn btn-primary" id="login-button">',lang_get('log_in'),'</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    ';
}