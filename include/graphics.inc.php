<?php

function head($title = '') {
    echo '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>',($title ? htmlspecialchars($title) . ' : ' : '') , CONFIG_SITE_NAME, ' - ', CONFIG_SITE_SLOGAN,'</title>
    <meta name="description" content="',CONFIG_SITE_DESCRIPTION,'">
    <meta name="author" content="">
    <link rel="icon" href="img/favicon.png" type="image/png" />

    <!-- CSS -->
    <link href="https://netdna.bootstrapcdn.com/twitter-bootstrap/2.3.1/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/common.css" rel="stylesheet">

    <!-- JS -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
    <script src="https://netdna.bootstrapcdn.com/twitter-bootstrap/2.3.2/js/bootstrap.min.js"></script>

    <script type="text/javascript">
    var globDict = {};
    '.(user_is_logged_in() ? 'globDict["user_id"] = '.$_SESSION['id'].';' : '').'
    </script>

    <script type="text/javascript" src="js/mellivora.js"></script>
</head>

<body>

<div class="container">

    <div class="masthead">
        <h3 class="muted">',CONFIG_SITE_NAME,'<img src="',CONFIG_SITE_LOGO,'" id="site_logo"/></h3>

<div class="navbar">
    <div class="navbar-inner">
        <div class="container">
            <ul class="nav">';

                $requested_filename = requested_file_name();

                if (user_is_logged_in()) {

                    if (user_is_staff()) {
                        echo '<li',(requested_file_name() == 'manage' ? ' class="active"' : ''),'><a href="manage">Manage</a></li>';
                    }

                    echo '
                    <li',($requested_filename == 'home' ? ' class="active"' : ''),'><a href="home">Home</a></li>
                    <li',($requested_filename == 'challenges' ? ' class="active"' : ''),'><a href="challenges">Challenges</a></li>
                    <li',($requested_filename == 'hints' ? ' class="active"' : ''),'><a href="hints">Hints</a></li>
                    <li',($requested_filename == 'scores' ? ' class="active"' : ''),'><a href="scores">Scores</a></li>
                    <li',($requested_filename == 'logout' ? ' class="active"' : ''),'><a href="logout">Log out</a></li>
                    ';
                    
                } else {
                echo '
                    <li',($requested_filename == 'home' ? ' class="active"' : ''),'><a href="home">Home</a></li>
                    <li',($requested_filename == 'login' ? ' class="active"' : ''),'><a href="login">Log in / Register</a></li>
                    <li',($requested_filename == 'scores' ? ' class="active"' : ''),'><a href="scores">Scores</a></li>
                ';
                }
                echo '
            </ul>
        </div>
    </div>
</div><!-- /.navbar -->
</div>';

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
    echo '
    <hr>

    <div class="footer">

    </div>

</div> <!-- /container -->

</body>
</html>';
}

function section_head ($title, $strip_html = true) {
    echo '<div class="page-header"><h2>',($strip_html ? htmlspecialchars($title) : $title),'</h2></div>';
}

function section_subhead ($title, $strip_html = true) {
    echo '<div class="page-header"><h1><small>',($strip_html ? htmlspecialchars($title) : $title),'</small></h1></div>';
}

function message_error ($message, $head = true, $foot = true, $exit = true) {
    if ($head) {
        head('Error');
    }

    echo section_subhead('Error');

    echo htmlspecialchars($message);

    if ($foot) {
        foot();
    }

    if ($exit) {
        exit();
    }
}

function message_generic ($title, $message, $head = true, $foot = true, $exit = true) {
    if ($head) {
        head($title);
    }

    echo section_subhead($title);

    echo htmlspecialchars($message);

    if ($foot) {
        foot();
    }

    if ($exit) {
        exit();
    }
}

function message_inline_bland ($message) {
    echo '<p>',htmlspecialchars($message),'</p>';
}

function message_inline_info ($message) {
    echo '<div class="alert alert-info">',htmlspecialchars($message),'</div>';
}

function message_inline_error ($message) {
    echo '<div class="alert alert-error">',htmlspecialchars($message),'</div>';
}

function message_inline_warning ($message) {
    echo '<div class="alert alert-error">',htmlspecialchars($message),'</div>';
}

function menu_management () {
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
      <li><a href="test_restrict_email.php">Test email</a></li>
    </ul>
</div><!-- /btn-group -->

<div class="btn-group">
    <button class="btn btn-warning dropdown-toggle" data-toggle="dropdown">Hints <span class="caret"></span></button>
    <ul class="dropdown-menu">
      <li><a href="new_hint.php">New hint</a></li>
      <li><a href="list_hints.php">List hints</a></li>
    </ul>
</div><!-- /btn-group -->

<div class="btn-group">
    <button class="btn btn-warning dropdown-toggle" data-toggle="dropdown">Exceptions <span class="caret"></span></button>
    <ul class="dropdown-menu">
      <li><a href="list_exceptions.php">List exceptions</a></li>
    </ul>
</div><!-- /btn-group -->
';
}

function get_position_medal ($position) {
    switch ($position) {
        case 1:
            return '<img src="img/award_star_gold_3.png" title="First to solve this challenge!" alt="First to solve this challenge!" />';
        case 2:
            return '<img src="img/award_star_silver_3.png" title="Second to solve this challenge!" alt="Second to solve this challenge!" />';
        case 3:
            return '<img src="img/award_star_bronze_3.png" title="Third to solve this challenge!" alt="Third to solve this challenge!" />';
    }
}

function bbcode_manual () {
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

function display_captcha() {
    require_once(CONFIG_PATH_THIRDPARTY . 'recaptcha/recaptchalib.php');

    echo '
        <script type="text/javascript">
         var RecaptchaOptions = {
                theme : "clean"
         };
         </script>
         ';

    echo '<p>', recaptcha_get_html(CONFIG_RECAPTCHA_PUBLIC_KEY, null, CONFIG_SSL_COMPAT), '</p>';
}

function scoreboard ($stmt) {
    echo '
    <table class="table table-striped table-hover">
      <thead>
        <tr>
          <th>#</th>
          <th>Team name</th>
          <th>Points</th>
        </tr>
      </thead>
      <tbody>
     ';

    $i = 1;
    while($place = $stmt->fetch(PDO::FETCH_ASSOC)) {

        echo '
        <tr>
          <td>',($place['competing'] ? number_format($i++) : ''),'</td>
          <td>
            <a href="user?id=',htmlspecialchars($place['user_id']),'">
              <span class="team_',htmlspecialchars($place['user_id']),'">
                ',htmlspecialchars($place['team_name']),'
              </span>
            </a>
          </td>
          <td>',($place['competing'] ? number_format($place['score']) : '<s>'.number_format($place['score']).'</s>'),'</td>
        </tr>
        ';
    }

    echo '
      </tbody>
    </table>
    ';
}

function form_start($enctype='', $class='') {
    echo '<form method="post" class="',($class ? $class : 'form-horizontal'),'"',($enctype ? ' enctype="'.$enctype.'"' : ''),'>';
}

function form_end() {
    echo '</form>';
}

function form_hidden ($name, $value) {
    echo '<input type="hidden" name="',htmlspecialchars($name),'" value="',htmlspecialchars($value),'" />';
}

function form_file ($name) {
    $name = htmlspecialchars($name);
    $field_name = strtolower(str_replace(' ','_',$name));
    echo '<input type="file" name="',$field_name,'" id="',$field_name,'" />';
}

function form_input_text($name, $prefill = '') {
    $name = htmlspecialchars($name);
    $field_name = strtolower(str_replace(' ','_',$name));
    echo '
    <div class="control-group">
      <label class="control-label" for="',$field_name,'">',$name,'</label>
      <div class="controls">
          <input type="text" id="',$field_name,'" name="',$field_name,'" class="input-block-level" placeholder="',$name,'"',($prefill ? ' value="'.htmlspecialchars($prefill).'"' : ''),' />
      </div>
    </div>
    ';
}

function form_input_checkbox ($name, $checked = 0) {
    $name = htmlspecialchars($name);
    $field_name = strtolower(str_replace(' ','_',$name));
    echo '
    <div class="control-group">
      <label class="control-label" for="',$field_name,'">',$name,'</label>
      <div class="controls">
          <input type="checkbox" id="',$field_name,'" name="',$field_name,'" value="1"',($checked ? ' checked="checked"' : ''),' />
      </div>
    </div>
    ';
}

function form_textarea($name, $prefill = '') {
    $name = htmlspecialchars($name);
    $field_name = strtolower(str_replace(' ','_',$name));
    echo '
    <div class="control-group">
      <label class="control-label" for="',$field_name,'">',$name,'</label>
      <div class="controls">
          <textarea id="',$field_name,'" name="',$field_name,'" class="input-block-level" rows="10">',($prefill ? htmlspecialchars($prefill) : ''),'</textarea>
      </div>
    </div>
    ';
}

function form_button_submit ($name, $type = 'primary') {
    $name = htmlspecialchars($name);
    $field_name = strtolower(str_replace(' ','_',$name));
    echo '
    <div class="control-group">
      <label class="control-label" for="',$field_name,'"></label>
      <div class="controls">
          <button type="submit" id="',$field_name,'" class="btn btn-',htmlspecialchars($type),'">',$name,'</button>
      </div>
    </div>
    ';
}

function form_select ($stmt, $name, $value, $selected, $option, $optgroup='') {
    $name = htmlspecialchars($name);
    $field_name = strtolower(str_replace(' ','_',$name));
    echo '
    <div class="control-group">
        <label class="control-label" for="',$field_name,'">',$name,'</label>
        <div class="controls">

        <select id="',$field_name,'" name="',$field_name,'">';

    $group = '';
    while ($opt = $stmt->fetch(PDO::FETCH_ASSOC)) {

        if ($optgroup && $group != $opt[$optgroup]) {
            if ($group) {
                echo '</optgroup>';
            }
            echo '<optgroup label="',htmlspecialchars($opt[$optgroup]),'">';
        }

        echo '<option value="',htmlspecialchars($opt[$value]),'"',($opt[$value] == $selected ? ' selected="selected"' : ''),'>', htmlspecialchars($opt[$option]), '</option>';

        if ($optgroup) {
            $group = $opt[$optgroup];
        }
    }

    if ($optgroup) {
        echo '</optgroup>';
    }

    echo '
        </select>

        </div>
    </div>
    ';
}

function form_bbcode_manual () {
    echo '
    <div class="control-group">
      <label class="control-label" for="bbcode">BBcode</label>
      <div class="controls">
          ',bbcode_manual(),'
      </div>
    </div>
    ';
}
