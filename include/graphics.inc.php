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
    <style type="text/css">
        body {
            padding-top: 20px;
            padding-bottom: 60px;
        }

            /* Custom container */
        .container {
            margin: 0 auto;
            max-width: 1000px;
        }
        .container > hr {
            margin: 60px 0;
        }

        /* Customize the navbar links to be fill the entire space of the .navbar */
        .navbar .navbar-inner {
            padding: 0;
        }
        .navbar .nav {
            margin: 0;
            display: table;
            width: 100%;
        }
        .navbar .nav li {
            display: table-cell;
            width: 1%;
            float: none;
        }
        .navbar .nav li a {
            font-weight: bold;
            text-align: center;
            border-left: 1px solid rgba(255,255,255,.75);
            border-right: 1px solid rgba(0,0,0,.1);
        }
        .navbar .nav li:first-child a {
            border-left: 0;
            border-radius: 3px 0 0 3px;
        }
        .navbar .nav li:last-child a {
            border-right: 0;
            border-radius: 0 3px 3px 0;
        }
    </style>

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="bootstrap/css/bootstrap-responsive.css" rel="stylesheet">

    <link rel="icon" href="img/favicon.png" type="image/png" />
</head>

<body>

<div class="container">

    <div class="masthead">
        <h3 class="muted"><? echo CONFIG_SITE_NAME ?><img src="img/favicon.png"/></h3>

<div class="navbar">
    <div class="navbar-inner">
        <div class="container">
            <ul class="nav">
                <?php
                if ($_SESSION['id']) {

                    if ($_SESSION['class'] >= CONFIG_UC_MODERATOR) {
                        echo '<li',(getRequestedFileName() == 'manage' ? ' class="active"' : ''),'><a href="manage">Manage</a></li>';
                    }

                ?>
                    <li<?php echo (getRequestedFileName() == 'home' ? ' class="active"' : '') ?>><a href="home">Home</a></li>
                    <li<?php echo (getRequestedFileName() == 'challenges' ? ' class="active"' : '') ?>><a href="challenges">Challenges</a></li>
                    <li<?php echo (getRequestedFileName() == 'scores' ? ' class="active"' : '') ?>><a href="scores">Scores</a></li>
                    <li<?php echo (getRequestedFileName() == 'logout' ? ' class="active"' : '') ?>><a href="logout">Log out</a></li>
                <?php
                } else {
                ?>
                    <li<?php echo (getRequestedFileName() == 'login' ? ' class="active"' : '') ?>><a href="login">Log in / Register</a></li>
                    <li<?php echo (getRequestedFileName() == 'scores' ? ' class="active"' : '') ?>><a href="scores">Scores</a></li>
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
        <div class="alert alert-warning">
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

function sectionHead ($title) {
    echo '<div class="page-header"><h2>',htmlspecialchars($title),'</h2></div>';
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
';
}