<?php

function head($title = '') {
    header('Content-Type: text/html; charset=utf-8');
    echo '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>',($title ? htmlspecialchars($title) . ' : ' : '') , CONFIG_SITE_NAME, ' - ', CONFIG_SITE_SLOGAN,'</title>
    <meta name="description" content="',CONFIG_SITE_DESCRIPTION,'">
    <meta name="author" content="">
    <link rel="icon" href="',CONFIG_SITE_URL,'img/favicon.png" type="image/png" />

    <!-- CSS -->
    <link href="https://netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap.min.css" rel="stylesheet">
    <link href="',CONFIG_SITE_URL,'css/mellivora.css" rel="stylesheet">

    <!-- JS -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
    <script src="https://netdna.bootstrapcdn.com/bootstrap/3.1.1/js/bootstrap.min.js"></script>

    ',js_global_dict(),'

    <script type="text/javascript" src="',CONFIG_SITE_URL,'js/mellivora.js"></script>
</head>

<body>

<div class="container" id="body-container">

    <div class="header" id="header">

            <ul class="nav nav-pills pull-right" id="menu-main">';

                    $requested_filename = requested_file_name();

                    if (user_is_logged_in()) {

                        if (user_is_staff()) {
                            echo '<li',(requested_file_name() == 'manage' ? ' class="active"' : ''),'><a href="',CONFIG_SITE_ADMIN_URL,'manage">Manage</a></li>';
                        }

                        echo '
                        <li',($requested_filename == 'home' ? ' class="active"' : ''),'><a href="',CONFIG_SITE_URL,'home">Home</a></li>
                        <li',($requested_filename == 'challenges' ? ' class="active"' : ''),'><a href="',CONFIG_SITE_URL,'challenges">Challenges</a></li>
                        <li',($requested_filename == 'hints' ? ' class="active"' : ''),'><a href="',CONFIG_SITE_URL,'hints">Hints</a></li>
                        <li',($requested_filename == 'scores' ? ' class="active"' : ''),'><a href="',CONFIG_SITE_URL,'scores">Scores</a></li>
                        <li',($requested_filename == 'profile' ? ' class="active"' : ''),'><a href="',CONFIG_SITE_URL,'profile">Profile</a></li>
                        <li',($requested_filename == 'logout' ? ' class="active"' : ''),'><a href="',CONFIG_SITE_URL,'logout">Log out</a></li>
                        ';

                    } else {
                    echo '
                        <li',($requested_filename == 'home' ? ' class="active"' : ''),'><a href="',CONFIG_SITE_URL,'home">Home</a></li>
                        <li',($requested_filename == 'login' ? ' class="active"' : ''),'><a href="',CONFIG_SITE_URL,'login">Log in / Register</a></li>
                        <li',($requested_filename == 'scores' ? ' class="active"' : ''),'><a href="',CONFIG_SITE_URL,'scores">Scores</a></li>
                    ';
                    }
                    echo '
            </ul>

            <h3 class="text-muted">',CONFIG_SITE_NAME,'<img src="',CONFIG_SITE_LOGO,'" id="site-logo"/></h3>
    </div><!-- navbar -->

    <div id="content-container">
    ';

    if (isset($_GET['generic_success'])) {
        message_inline_green('<h3>Success!</h3>', false);
    } else if (isset($_GET['generic_warning'])) {
        message_inline_red('<h3>Something went wrong! Most likely the action you attempted has failed.</h3>', false);
    }
}

function foot () {
    echo '

    </div> <!-- / content container -->

    <div class="footer" id="footer">

    </div>

</div> <!-- /container -->

</body>
</html>';
}

function section_head ($title, $tagline = '', $strip_html = true) {
    echo '
    <div class="row">
        <div class="col-lg-12">
          <h2 class="page-header">',($strip_html ? htmlspecialchars($title) : $title),' ',($tagline ? $strip_html ? '<small>'.htmlspecialchars($tagline).'</small>' : '<small>'.$tagline.'</small>' : ''),'</h2>
        </div>
    </div>
    ';
}

function section_subhead ($title, $tagline = '', $strip_html = true) {
    echo '
    <div class="row">
        <div class="col-lg-12">
          <h3 class="page-header">',($strip_html ? htmlspecialchars($title) : $title),' ',($tagline ? $strip_html ? '<small>'.htmlspecialchars($tagline).'</small>' : '<small>'.$tagline.'</small>' : ''),'</h3>
        </div>
    </div>
    ';
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

function message_inline_blue ($message, $strip_html = true) {
    echo '<div class="alert alert-info">',($strip_html ? htmlspecialchars($message) : $message),'</div>';
}

function message_inline_red ($message, $strip_html = true) {
    echo '<div class="alert alert-danger">',($strip_html ? htmlspecialchars($message) : $message),'</div>';
}

function message_inline_yellow ($message, $strip_html = true) {
    echo '<div class="alert alert-warning">',($strip_html ? htmlspecialchars($message) : $message),'</div>';
}

function message_inline_green ($message, $strip_html = true) {
    echo '<div class="alert alert-success">',($strip_html ? htmlspecialchars($message) : $message),'</div>';
}

function menu_management () {
    echo '
<div id="menu-management">
    <div class="btn-group">
        <button class="btn btn-warning dropdown-toggle" data-toggle="dropdown">News <span class="caret"></span></button>
        <ul class="dropdown-menu">
          <li><a href="',CONFIG_SITE_ADMIN_URL,'new_news">Add news item</a></li>
          <li><a href="',CONFIG_SITE_ADMIN_URL,'list_news">List news items</a></li>
        </ul>
    </div><!-- /btn-group -->

    <div class="btn-group">
        <button class="btn btn-warning dropdown-toggle" data-toggle="dropdown">Categories <span class="caret"></span></button>
        <ul class="dropdown-menu">
          <li><a href="',CONFIG_SITE_ADMIN_URL,'new_category">Add category</a></li>
          <li><a href="',CONFIG_SITE_ADMIN_URL,'manage">List categories</a></li>
        </ul>
    </div><!-- /btn-group -->

    <div class="btn-group">
        <button class="btn btn-warning dropdown-toggle" data-toggle="dropdown">Challenges <span class="caret"></span></button>
        <ul class="dropdown-menu">
          <li><a href="',CONFIG_SITE_ADMIN_URL,'new_challenge">Add challenge</a></li>
          <li><a href="',CONFIG_SITE_ADMIN_URL,'manage">List challenges</a></li>
        </ul>
    </div><!-- /btn-group -->

    <div class="btn-group">
        <button class="btn btn-warning dropdown-toggle" data-toggle="dropdown">Submissions <span class="caret"></span></button>
        <ul class="dropdown-menu">
          <li><a href="',CONFIG_SITE_ADMIN_URL,'list_submissions">List submissions in need of marking</a></li>
          <li><a href="',CONFIG_SITE_ADMIN_URL,'list_submissions?all=1">List all submissions</a></li>
        </ul>
    </div><!-- /btn-group -->


    <div class="btn-group">
        <button class="btn btn-warning dropdown-toggle" data-toggle="dropdown">Users <span class="caret"></span></button>
        <ul class="dropdown-menu">
          <li role="presentation" class="dropdown-header">Users</li>
          <li><a href="',CONFIG_SITE_ADMIN_URL,'list_users">List users</a></li>
          <li role="presentation" class="dropdown-header">User types</li>
          <li><a href="',CONFIG_SITE_ADMIN_URL,'new_user_type">Add user type</a></li>
          <li><a href="',CONFIG_SITE_ADMIN_URL,'list_user_types">List user types</a></li>
        </ul>
    </div><!-- /btn-group -->

    <div class="btn-group">
        <button class="btn btn-warning dropdown-toggle" data-toggle="dropdown">Signup rules <span class="caret"></span></button>
        <ul class="dropdown-menu">
          <li><a href="',CONFIG_SITE_ADMIN_URL,'new_restrict_email">New rule</a></li>
          <li><a href="',CONFIG_SITE_ADMIN_URL,'list_restrict_email">List rules</a></li>
          <li><a href="',CONFIG_SITE_ADMIN_URL,'test_restrict_email">Test rule</a></li>
        </ul>
    </div><!-- /btn-group -->

    <div class="btn-group">
        <button class="btn btn-warning dropdown-toggle" data-toggle="dropdown">Email <span class="caret"></span></button>
        <ul class="dropdown-menu">
          <li><a href="',CONFIG_SITE_ADMIN_URL,'new_email">Single email</a></li>
          <li><a href="',CONFIG_SITE_ADMIN_URL,'new_email?bcc=all">Email all users</a></li>
        </ul>
    </div><!-- /btn-group -->

    <div class="btn-group">
        <button class="btn btn-warning dropdown-toggle" data-toggle="dropdown">Hints <span class="caret"></span></button>
        <ul class="dropdown-menu">
          <li><a href="',CONFIG_SITE_ADMIN_URL,'new_hint">New hint</a></li>
          <li><a href="',CONFIG_SITE_ADMIN_URL,'list_hints">List hints</a></li>
        </ul>
    </div><!-- /btn-group -->

    <div class="btn-group">
        <button class="btn btn-warning dropdown-toggle" data-toggle="dropdown">Exceptions <span class="caret"></span></button>
        <ul class="dropdown-menu">
          <li><a href="',CONFIG_SITE_ADMIN_URL,'list_exceptions">List exceptions</a></li>
        </ul>
    </div><!-- /btn-group -->
</div>
';
}

function get_position_medal ($position, $returnpos = false) {
    switch ($position) {
        case 1:
            return '<img src="'.CONFIG_SITE_URL.'img/award_star_gold_3.png" title="First to solve this challenge!" alt="First to solve this challenge!" />';
        case 2:
            return '<img src="'.CONFIG_SITE_URL.'img/award_star_silver_3.png" title="Second to solve this challenge!" alt="Second to solve this challenge!" />';
        case 3:
            return '<img src="'.CONFIG_SITE_URL.'img/award_star_bronze_3.png" title="Third to solve this challenge!" alt="Third to solve this challenge!" />';
    }

    if ($returnpos) {
        return '#'.$position.', ';
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
                <li>[size=6]...[/size]</li>
                <li>[color=red]...[/color]</li>
                <li>[font=verdana]...[/font]</li>
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

function scoreboard ($scores) {

    echo '
    <table class="table table-striped table-hover">
      <thead>
        <tr>
          <th>#</th>
          <th>Team</th>
          <th class="text-center">Country</th>
          <th>Points</th>
        </tr>
      </thead>
      <tbody>
     ';

    $i = 1;
    foreach($scores as $score) {

        echo '
        <tr>
          <td>',($score['competing'] ? number_format($i++) : ''),'</td>
          <td>
            <a href="user?id=',htmlspecialchars($score['user_id']),'">
              <span class="team_',htmlspecialchars($score['user_id']),'">
                ',htmlspecialchars($score['team_name']),'
              </span>
            </a>
          </td>
          <td class="text-center">
            ',country_flag_link($score['country_name'], $score['country_code']),'
          </td>
          <td>',number_format($score['score']),'</td>
        </tr>
        ';
    }

    echo '
      </tbody>
    </table>
    ';
}

function form_start($action='', $class='', $enctype='') {
    echo '<form method="post" class="',($class ? $class : 'form-horizontal'),'"',($enctype ? ' enctype="'.$enctype.'"' : ''),'',($action ? 'action="'.CONFIG_SITE_URL.$action.'"' : ''),' role="form">';
}

function form_end() {
    echo '</form>';
}

function form_hidden ($name, $value) {
    $name = htmlspecialchars($name);
    $field_name = strtolower(str_replace(' ','_',$name));
    echo '<input type="hidden" name="',$field_name,'" value="',htmlspecialchars($value),'" />';
}

function form_file ($name) {
    $name = htmlspecialchars($name);
    $field_name = strtolower(str_replace(' ','_',$name));
    echo '<input type="file" name="',$field_name,'" id="',$field_name,'" />';
}

function form_input_text($name, $prefill = false, $disabled = false) {
    $name = htmlspecialchars($name);
    $field_name = strtolower(str_replace(' ','_',$name));
    echo '
    <div class="form-group">
      <label class="col-sm-2 control-label" for="',$field_name,'">',$name,'</label>
      <div class="col-sm-10">
          <input type="text" id="',$field_name,'" name="',$field_name,'" class="form-control" placeholder="',$name,'"',($prefill !== false ? ' value="'.htmlspecialchars($prefill).'"' : ''),'',($disabled ? ' disabled' : ''),' />
      </div>
    </div>
    ';
}

function form_input_password($name, $prefill = false, $disabled = false) {
    $name = htmlspecialchars($name);
    $field_name = strtolower(str_replace(' ','_',$name));
    echo '
    <div class="form-group">
      <label class="col-sm-2 control-label" for="',$field_name,'">',$name,'</label>
      <div class="col-sm-10">
          <input type="password" id="',$field_name,'" name="',$field_name,'" class="form-control" placeholder="',$name,'"',($prefill !== false ? ' value="'.htmlspecialchars($prefill).'"' : ''),'',($disabled ? ' disabled' : ''),' required />
      </div>
    </div>
    ';
}

function form_input_checkbox ($name, $checked = 0) {
    $name = htmlspecialchars($name);
    $field_name = strtolower(str_replace(' ','_',$name));
    echo '
    <div class="form-group">
      <label class="col-sm-2 control-label" for="',$field_name,'">',$name,'</label>
      <div class="col-sm-10">
          <input type="checkbox" id="',$field_name,'" name="',$field_name,'" value="1"',($checked ? ' checked="checked"' : ''),' />
      </div>
    </div>
    ';
}

function form_textarea($name, $prefill = false) {
    $name = htmlspecialchars($name);
    $field_name = strtolower(str_replace(' ','_',$name));
    echo '
    <div class="form-group">
      <label class="col-sm-2 control-label" for="',$field_name,'">',$name,'</label>
      <div class="col-sm-10">
          <textarea id="',$field_name,'" name="',$field_name,'" class="form-control" rows="10">',($prefill !== false ? htmlspecialchars($prefill) : ''),'</textarea>
      </div>
    </div>
    ';
}

function form_button_submit ($name, $type = 'primary') {
    $name = htmlspecialchars($name);
    $field_name = strtolower(str_replace(' ','_',$name));
    echo '
    <div class="form-group">
      <label class="col-sm-2 control-label" for="',$field_name,'"></label>
      <div class="col-sm-10">
          <button type="submit" id="',$field_name,'" class="btn btn-',htmlspecialchars($type),'">',$name,'</button>
      </div>
    </div>
    ';
}

function form_select ($opts, $name, $value, $selected, $option, $optgroup='') {
    $name = htmlspecialchars($name);
    $field_name = strtolower(str_replace(' ','_',$name));
    echo '
    <div class="form-group">
        <label class="col-sm-2 control-label" for="',$field_name,'">',$name,'</label>
        <div class="col-sm-10">

        <select id="',$field_name,'" name="',$field_name,'">';

    $group = '';
    foreach ($opts as $opt) {

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
    <div class="form-group">
      <label class="col-sm-2 control-label" for="bbcode">BBcode</label>
      <div class="col-sm-10">
          ',bbcode_manual(),'
      </div>
    </div>
    ';
}

function js_global_dict () {

    $dict = array();
    if (user_is_logged_in()) {
        $dict['user_id'] = $_SESSION['id'];
    }

    echo '<script type="text/javascript">
        var global_dict = {};
        ';

    foreach ($dict as $key => $val) {
        echo 'global_dict["',htmlspecialchars($key),'"] = "',htmlspecialchars($val),'"';
    }

    echo '
    </script>';
}

function progress_bar ($percent, $type = false) {

    if (!$type) {
        $type = ($percent >= 100 ? 'success' : 'info');
    }

    echo '
    <div class="progress progress-striped">
        <div class="progress-bar progress-bar-',$type,'" role="progressbar" aria-valuenow="',$percent,'" aria-valuemin="0" aria-valuemax="100" style="width: ',$percent,'%">
            <span class="sr-only">',$percent,'% complete</span>
        </div>
    </div>
    ';
}

function print_ri($val){
    echo '<pre>',print_r($val),'</pre>';
}

function country_flag_link($country_name, $country_code, $return = false) {
    $country_name = htmlspecialchars($country_name);
    $country_code = htmlspecialchars($country_code);
    
    $flag_link = '
    <a href="country?code='.htmlspecialchars($country_code).'">
        <img src="'.CONFIG_SITE_URL.'img/flags/'.$country_code.'.png" alt="'.$country_code.'" title="'.$country_name.'" />
    </a>';
    
    if ($return) {
        return $flag_link;
    }

    echo $flag_link;
}

function user_ip_log($user_id) {

    validate_id($user_id);

    echo '
        <table id="files" class="table table-striped table-hover">
          <thead>
            <tr>
              <th>IP</th>
              <th>Hostname</th>
              <th>First used</th>
              <th>Last used</th>
              <th>Times used</th>
            </tr>
          </thead>
          <tbody>
        ';

    $entries = db_select_all(
        'ip_log',
        array(
            'INET_NTOA(ip) AS ip',
            'added',
            'last_used',
            'times_used'
        ),
        array('user_id' => $_GET['id'])
    );

    foreach($entries as $entry) {
        echo '
        <tr>
            <td><a href="list_ip_log.php?ip=',htmlspecialchars($entry['ip']),'">',htmlspecialchars($entry['ip']),'</a></td>
            <td>',gethostbyaddr($entry['ip']),'</td>
            <td>',date_time($entry['added']),'</td>
            <td>',date_time($entry['last_used']),'</td>
            <td>',number_format($entry['times_used']),'</td>
        </tr>
        ';
    }

    echo '
          </tbody>
        </table>
         ';
}