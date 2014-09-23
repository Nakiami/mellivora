<?php

function login_dialog() {
    echo '
    <div class="modal fade" id="login-dialog" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="login-dialog-form" method="post" class="form-signin" action="actions/login">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                        <h4 class="modal-title" id="myModalLabel">Log in</h4>
                    </div>
                    <div class="modal-body">
                            <input name="',md5(CONFIG_SITE_NAME.'USR'),'" type="email" class="form-control" placeholder="Email address" required autofocus />
                            <input name="',md5(CONFIG_SITE_NAME.'PWD'), '" type="password" class="form-control" placeholder="Password" required />
                            <input type="hidden" name="action" value="login" />
                            <input type="hidden" name="redirect" value="', pathinfo(basename($_SERVER['PHP_SELF']), PATHINFO_FILENAME) ,'" />
                            <label class="checkbox">
                                <input type="checkbox" name="remember_me" value="1"> Remember me
                            </label>
                            <a href="reset_password">I\'ve forgotten my password</a>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Log in</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    ';
}

?>