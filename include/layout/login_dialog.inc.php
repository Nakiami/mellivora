<?php

function login_dialog() {
    echo '
    <div class="modal fade" id="login-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title" id="myModalLabel">Log in</h4>
                </div>
                <div class="modal-body">
                    <form id="login-dialog-form" method="post" class="form-signin" action="actions/login">
                        <input name="',md5(CONFIG_SITE_NAME.'USR'),'" type="email" class="form-control" placeholder="Email address" required autofocus />
                        <input name="',md5(CONFIG_SITE_NAME.'PWD'), '" type="password" class="form-control" placeholder="Password" required />
                        <input type="hidden" name="action" value="login" />
                        <label class="checkbox">
                            <input type="checkbox" name="remember_me" value="1"> Remember me
                        </label>
                        <a href="reset_password">I\'ve forgotten my password</a>
                        <button style="display: none;" type="submit"></button> <!-- The clickable button is outside the form -->
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="$(\'#login-dialog-form\').submit()">Log in</button>
                </div>
            </div>
        </div>
    </div>
    ';
}

?>