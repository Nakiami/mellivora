<?php

if (!defined('IN_FILE')) {
    exit(); // TODO report error
}

function head($title = '') {
    ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title><? echo ($title ? htmlspecialchars($title) . ' : ' : '') , CONFIG_SITE_NAME, ' - ', CONFIG_SITE_SLOGAN ?></title>
    <meta name="description" content="<? echo CONFIG_SITE_DESCRIPTION ?>">
    <meta name="author" content="">

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
    <script src="https://netdna.bootstrapcdn.com/twitter-bootstrap/2.3.2/js/bootstrap.min.js"></script>

    <link href="bootstrap/css/bootstrap.css" rel="stylesheet">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="bootstrap/css/bootstrap-responsive.css" rel="stylesheet">

    <!-- bootstrap mods -->
    <link href="css/common.css" rel="stylesheet">

    <link rel="icon" href="img/favicon.png" type="image/png" />
</head>

<body>

<div class="container">

    <div class="masthead">
        <h3 class="muted"><? echo CONFIG_SITE_NAME ?><img src="<?php echo CONFIG_SITE_LOGO ?>" id="site_logo"/></h3>

<div class="navbar">
    <div class="navbar-inner">
        <div class="container">
            <ul class="nav">
                <?php

                $requested_filename = getRequestedFileName();

                if (userLoggedIn()) {

                    if ($_SESSION['class'] >= CONFIG_UC_MODERATOR) {
                        echo '<li',(getRequestedFileName() == 'manage' ? ' class="active"' : ''),'><a href="manage">Manage</a></li>';
                    }

                    echo '
                    <li',($requested_filename == 'home' ? ' class="active"' : ''),'><a href="home">Home</a></li>
                    <li',($requested_filename == 'challenges' ? ' class="active"' : ''),'><a href="challenges">Challenges</a></li>
                    <li',($requested_filename == 'hints' ? ' class="active"' : ''),'><a href="hints">Hints</a></li>
                    <li',($requested_filename == 'scores' ? ' class="active"' : ''),'><a href="scores">Scores</a></li>
                    <li',($requested_filename == 'logout' ? ' class="active"' : ''),'><a href="logout">Log out</a></li>
                    ';
                    
                } else {
                ?>
                    <li<?php echo ($requested_filename == 'home' ? ' class="active"' : '') ?>><a href="home">Home</a></li>
                    <li<?php echo ($requested_filename == 'login' ? ' class="active"' : '') ?>><a href="login">Log in / Register</a></li>
                    <li<?php echo ($requested_filename == 'scores' ? ' class="active"' : '') ?>><a href="scores">Scores</a></li>
                <?php
                }
                ?>
            </ul>
        </div>
    </div>
</div><!-- /.navbar -->
</div>
    <?php

    if (isset($_GET['generic_success'])) {
        echo '
        <div class="alert alert-success">
            <h3>Success!</h3>
        </div>
        ';
    }

    else if (isset($_GET['generic_warning'])) {
        echo '
        <div class="alert alert-danger">
            <h3>Something failed!</h3>
        </div>
        ';
    }
}

function foot () {
    ?>
    <hr>

    <div class="footer">

    </div>

</div> <!-- /container -->

</body>
</html>

<?php
}

function sectionHead ($title, $strip_html = true) {
    echo '<div class="page-header"><h2>',($strip_html ? htmlspecialchars($title) : $title),'</h2></div>';
}

function sectionSubHead ($title, $strip_html = true) {
    echo '<div class="page-header"><h1><small>',($strip_html ? htmlspecialchars($title) : $title),'</small></h1></div>';
}

function errorMessage ($message, $head = true, $foot = true, $exit = true) {
    if ($head) {
        head('Error');
    }

    echo sectionSubHead('Error');

    echo htmlspecialchars($message);

    if ($foot) {
        foot();
    }

    if ($exit) {
        exit();
    }
}

function genericMessage ($title, $message, $head = true, $foot = true, $exit = true) {
    if ($head) {
        head($title);
    }

    echo sectionSubHead($title);

    echo htmlspecialchars($message);

    if ($foot) {
        foot();
    }

    if ($exit) {
        exit();
    }
}

function managementMenu () {
    echo '
<div class="btn-group">
    <button class="btn btn-warning dropdown-toggle" data-toggle="dropdown">News <span class="caret"></span></button>
    <ul class="dropdown-menu">
      <li><a href="new_news.php">Add news item</a></li>
      <li><a href="list_news.php">List news items</a></li>
    </ul>
</div><!-- /btn-group -->

<div class="btn-group">
    <button class="btn btn-warning dropdown-toggle" data-toggle="dropdown">Categories <span class="caret"></span></button>
    <ul class="dropdown-menu">
      <li><a href="new_category.php">Add category</a></li>
    </ul>
</div><!-- /btn-group -->

<div class="btn-group">
    <button class="btn btn-warning dropdown-toggle" data-toggle="dropdown">Challenges <span class="caret"></span></button>
    <ul class="dropdown-menu">
      <li><a href="new_challenge.php">Add challenge</a></li>
    </ul>
</div><!-- /btn-group -->

<div class="btn-group">
    <button class="btn btn-warning dropdown-toggle" data-toggle="dropdown">Submissions <span class="caret"></span></button>
    <ul class="dropdown-menu">
      <li><a href="list_submissions.php">List submissions</a></li>
    </ul>
</div><!-- /btn-group -->


<div class="btn-group">
    <button class="btn btn-warning dropdown-toggle" data-toggle="dropdown">Users <span class="caret"></span></button>
    <ul class="dropdown-menu">
      <li><a href="list_users.php">List users</a></li>
    </ul>
</div><!-- /btn-group -->

<div class="btn-group">
    <button class="btn btn-warning dropdown-toggle" data-toggle="dropdown">Email signup rules <span class="caret"></span></button>
    <ul class="dropdown-menu">
      <li><a href="new_restrict_email.php">New rule</a></li>
      <li><a href="list_restrict_email.php">List rules</a></li>
    </ul>
</div><!-- /btn-group -->

<div class="btn-group">
    <button class="btn btn-warning dropdown-toggle" data-toggle="dropdown">Hints <span class="caret"></span></button>
    <ul class="dropdown-menu">
      <li><a href="new_hint.php">New hint</a></li>
      <li><a href="list_hints.php">List hints</a></li>
    </ul>
</div><!-- /btn-group -->
';
}

function getPositionMedal ($position) {
    switch ($position) {
        case 1:
            return '<img src="img/award_star_gold_3.png" title="First to solve this challenge!" alt="First to solve this challenge!" />';
        case 2:
            return '<img src="img/award_star_silver_3.png" title="Second to solve this challenge!" alt="Second to solve this challenge!" />';
        case 3:
            return '<img src="img/award_star_bronze_3.png" title="Third to solve this challenge!" alt="Third to solve this challenge!" />';
    }
}

function bbCodeManual () {
    echo '
    <table>
        <tr>
        <td>
            <ul>
            <li><b>Text Styles:</b>
                <ul>
                <li>[b]...[/b]</li>
                <li>[i]...[/i]</li>
                <li>[u]...[/u]</li>
                <li>[s]...[/s]</li>
                <li>[sup]...[/sup]</li>
                <li>[sub]...[/sub]</li>
                <li>[spoiler]...[/spoiler]</li>
                <li>[acronym]...[/acronym]</li>
                <li>[size]...[/size]</li>
                <li>[color]...[/color]</li>
                <li>[font]...[/font]</li>
                </ul>
            </li>
            <li><b>Links:</b>
                <ul>
                <li>[url]...[/url]</li>
                <li>[url=...]text[/url]</li>
                <li>[email]...[/email]</li>
                <li>[wiki]</li>
                </ul>
            </li>
            </ul>
        </td>
        <td>
            <ul>
            <li><b>Replaced Items:</b>
                <ul>
                <li>[img]...[/img]</li>
                <li>[rule]</li>
                <li>[br]</li>
                </ul>
            </li>
            <li><b>Alignment:</b>
                <ul>
                <li>[center]...[/center]</li>
                <li>[left]...[/left]</li>
                <li>[right]...[/right]</li>
                <li>[indent]...[/indent]</li>
                </ul>
            </li>
            <li><b>Columns:</b>
                <ul>
                <li>[columns]...[/columns]</li>
                <li>[nextcol]</li>
                </ul>
            </li>
            <li><b>Containers:</b>
                <ul>
                <li>[code]...[/code]</li>
                <li>[quote]...[/quote]</li>
                </ul>
            </li>
            </ul>
        </td>

        <td>
            <ul>
            <li><b>Lists:</b>
                <ul>
                <li>[list]...[/list]</li>
                <li>[*]...</li>
                </ul>
            </li>
            </ul>
        </td>
        </tr>
    </table>
    ';
}

function displayCaptcha() {
    require_once(CONFIG_ABS_PATH . 'include/recaptcha/recaptchalib.php');

    echo '
        <script type="text/javascript">
         var RecaptchaOptions = {
                theme : "clean"
         };
         </script>
         ';

    echo '<p>', recaptcha_get_html(CONFIG_RECAPTCHA_PUBLIC_KEY, null, CONFIG_SSL_COMPAT), '</p>';
}