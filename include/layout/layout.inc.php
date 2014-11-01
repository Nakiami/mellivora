<?php
require(CONFIG_PATH_LAYOUT . 'login_dialog.inc.php');
require(CONFIG_PATH_LAYOUT . 'messages.inc.php');
require(CONFIG_PATH_LAYOUT . 'scores.inc.php');
require(CONFIG_PATH_LAYOUT . 'forms.inc.php');
require(CONFIG_PATH_LAYOUT . 'challenges.inc.php');
require(CONFIG_PATH_LAYOUT . 'dynamic.inc.php');

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
    <link href="',CONFIG_SITE_URL,'css/header.css" rel="stylesheet">';

    js_global_dict();

    if (CONFIG_SEGMENT_IO_KEY) {
        echo '
        <script type="text/javascript">
        window.analytics=window.analytics||[],window.analytics.methods=["identify","group","track","page","pageview","alias","ready","on","once","off","trackLink","trackForm","trackClick","trackSubmit"],window.analytics.factory=function(t){return function(){var a=Array.prototype.slice.call(arguments);return a.unshift(t),window.analytics.push(a),window.analytics}};for(var i=0;i<window.analytics.methods.length;i++){var key=window.analytics.methods[i];window.analytics[key]=window.analytics.factory(key)}window.analytics.load=function(t){if(!document.getElementById("analytics-js")){var a=document.createElement("script");a.type="text/javascript",a.id="analytics-js",a.async=!0,a.src=("https:"===document.location.protocol?"https://":"http://")+"cdn.segment.io/analytics.js/v1/"+t+"/analytics.min.js";var n=document.getElementsByTagName("script")[0];n.parentNode.insertBefore(a,n)}},window.analytics.SNIPPET_VERSION="2.0.9",
        window.analytics.load("',CONFIG_SEGMENT_IO_KEY,'");
        window.analytics.page();
        </script>
        ';
    }

echo '
</head>

<body>';

if (!user_is_logged_in()) {
    login_dialog();
}

echo '

<nav class="header" id="header">
    <div id="header-inner">
        <div id="header-logo">
            <a href="',CONFIG_SITE_URL,'">
                <h3 id="site-logo-text">',CONFIG_SITE_NAME,'</h3>
                <div id="site-logo"/></div>
            </a>
        </div>
        <div id="header-menu">
            <ul class="nav nav-pills pull-right" id="menu-main">';

                if (user_is_logged_in()) {

                    if (user_is_staff()) {
                        echo '<li><a href="',CONFIG_SITE_ADMIN_URL,'">Manage</a></li>';
                    }

                    echo '
                        <li><a href="',CONFIG_SITE_URL,'home">Home</a></li>
                        <li><a href="',CONFIG_SITE_URL,'challenges">Challenges</a></li>
                        <li><a href="',CONFIG_SITE_URL,'hints">Hints</a></li>
                        <li><a href="',CONFIG_SITE_URL,'scores">Scores</a></li>
                        <li><a href="',CONFIG_SITE_URL,'profile">Profile</a></li>
                        ',dynamic_menu_content(),'
                        <li><a href="',CONFIG_SITE_URL,'logout">Log out</a></li>
                        ';

                } else {
                    echo '
                        <li><a href="',CONFIG_SITE_URL,'home">Home</a></li>
                        <li><a href="',CONFIG_SITE_URL,'scores">Scores</a></li>
                        ',dynamic_menu_content(),'
                        <li><a href="',CONFIG_SITE_URL,'register">Register</a></li>
                        <li><a href="" data-toggle="modal" data-target="#login-dialog">Log in</a></li>
                    ';
                }
                echo '
            </ul>
        </div>
    </div>
</nav><!-- navbar -->

<div class="container" id="body-container">

    <div id="content-container">
    ';

    if (isset($_GET['generic_success'])) {
        message_inline_green('<h3>Success!</h3>', false);
    } else if (isset($_GET['generic_failure'])) {
        message_inline_red('<h3>Failure!</h3>', false);
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

<!-- JS -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
<script src="https://netdna.bootstrapcdn.com/bootstrap/3.1.1/js/bootstrap.min.js"></script>
<script type="text/javascript" src="',CONFIG_SITE_URL,'js/mellivora.js"></script>

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

function menu_management () {
    echo '
<div id="menu-management">
    <div class="btn-group">
        <button class="btn btn-warning dropdown-toggle btn-sm" data-toggle="dropdown">News <span class="caret"></span></button>
        <ul class="dropdown-menu">
          <li><a href="',CONFIG_SITE_ADMIN_URL,'new_news">Add news item</a></li>
          <li><a href="',CONFIG_SITE_ADMIN_URL,'list_news">List news items</a></li>
        </ul>
    </div>

    <div class="btn-group">
        <button class="btn btn-warning dropdown-toggle btn-sm" data-toggle="dropdown">Categories <span class="caret"></span></button>
        <ul class="dropdown-menu">
          <li><a href="',CONFIG_SITE_ADMIN_URL,'new_category">Add category</a></li>
          <li><a href="',CONFIG_SITE_ADMIN_URL,'">List categories</a></li>
        </ul>
    </div>

    <div class="btn-group">
        <button class="btn btn-warning dropdown-toggle btn-sm" data-toggle="dropdown">Challenges <span class="caret"></span></button>
        <ul class="dropdown-menu">
          <li><a href="',CONFIG_SITE_ADMIN_URL,'new_challenge">Add challenge</a></li>
          <li><a href="',CONFIG_SITE_ADMIN_URL,'">List challenges</a></li>
        </ul>
    </div>

    <div class="btn-group">
        <button class="btn btn-warning dropdown-toggle btn-sm" data-toggle="dropdown">Submissions <span class="caret"></span></button>
        <ul class="dropdown-menu">
          <li><a href="',CONFIG_SITE_ADMIN_URL,'list_submissions">List submissions in need of marking</a></li>
          <li><a href="',CONFIG_SITE_ADMIN_URL,'list_submissions?all=1">List all submissions</a></li>
        </ul>
    </div>


    <div class="btn-group">
        <button class="btn btn-warning dropdown-toggle btn-sm" data-toggle="dropdown">Users <span class="caret"></span></button>
        <ul class="dropdown-menu">
          <li role="presentation" class="dropdown-header">Users</li>
          <li><a href="',CONFIG_SITE_ADMIN_URL,'list_users">List users</a></li>
          <li role="presentation" class="dropdown-header">User types</li>
          <li><a href="',CONFIG_SITE_ADMIN_URL,'new_user_type">Add user type</a></li>
          <li><a href="',CONFIG_SITE_ADMIN_URL,'list_user_types">List user types</a></li>
        </ul>
    </div>

    <div class="btn-group">
        <button class="btn btn-warning dropdown-toggle btn-sm" data-toggle="dropdown">Signup rules <span class="caret"></span></button>
        <ul class="dropdown-menu">
          <li><a href="',CONFIG_SITE_ADMIN_URL,'new_restrict_email">New rule</a></li>
          <li><a href="',CONFIG_SITE_ADMIN_URL,'list_restrict_email">List rules</a></li>
          <li><a href="',CONFIG_SITE_ADMIN_URL,'test_restrict_email">Test rule</a></li>
        </ul>
    </div>

    <div class="btn-group">
        <button class="btn btn-warning dropdown-toggle btn-sm" data-toggle="dropdown">Email <span class="caret"></span></button>
        <ul class="dropdown-menu">
          <li><a href="',CONFIG_SITE_ADMIN_URL,'new_email">Single email</a></li>
          <li><a href="',CONFIG_SITE_ADMIN_URL,'new_email?bcc=all">Email all users</a></li>
        </ul>
    </div>

    <div class="btn-group">
        <button class="btn btn-warning dropdown-toggle btn-sm" data-toggle="dropdown">Hints <span class="caret"></span></button>
        <ul class="dropdown-menu">
          <li><a href="',CONFIG_SITE_ADMIN_URL,'new_hint">New hint</a></li>
          <li><a href="',CONFIG_SITE_ADMIN_URL,'list_hints">List hints</a></li>
        </ul>
    </div>

    <div class="btn-group">
        <button class="btn btn-warning dropdown-toggle btn-sm" data-toggle="dropdown">Dynamic content <span class="caret"></span></button>
        <ul class="dropdown-menu">
          <li role="presentation" class="dropdown-header">Menu</li>
          <li><a href="',CONFIG_SITE_ADMIN_URL,'new_dynamic_menu_item">New menu item</a></li>
          <li><a href="',CONFIG_SITE_ADMIN_URL,'list_dynamic_menu">List menu items</a></li>
          <li role="presentation" class="dropdown-header">Pages</li>
          <li><a href="',CONFIG_SITE_ADMIN_URL,'new_dynamic_page">New page</a></li>
          <li><a href="',CONFIG_SITE_ADMIN_URL,'list_dynamic_pages">List pages</a></li>
        </ul>
    </div>

    <div class="btn-group">
        <button class="btn btn-warning dropdown-toggle btn-sm" data-toggle="dropdown">Exceptions <span class="caret"></span></button>
        <ul class="dropdown-menu">
          <li><a href="',CONFIG_SITE_ADMIN_URL,'list_exceptions">List exceptions</a></li>
        </ul>
    </div>
    
    <div class="btn-group">
        <button class="btn btn-warning dropdown-toggle btn-sm" data-toggle="dropdown">Search <span class="caret"></span></button>
        <ul class="dropdown-menu">
          <li><a href="',CONFIG_SITE_ADMIN_URL,'search">Search</a></li>
        </ul>
    </div>
</div>
';
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
            <td>',(CONFIG_GET_IP_HOST_BY_ADDRESS ? gethostbyaddr($entry['ip']) : '<i>Lookup disabled in config</i>'),'</td>
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

function user_exception_log($user_id, $limit = null) {

    validate_id($user_id);

    echo '
    <table id="hints" class="table table-striped table-hover">
      <thead>
        <tr>
          <th>Message</th>
          <th>Added</th>
          <th>IP</th>
          <th>Trace</th>
        </tr>
      </thead>
      <tbody>
    ';

    $exceptions = db_query_fetch_all('
        SELECT
           e.id,
           e.message,
           e.added,
           e.added_by,
           e.trace,
           INET_NTOA(e.user_ip) AS user_ip,
           u.team_name
        FROM exceptions AS e
        LEFT JOIN users AS u ON u.id = e.added_by
        WHERE e.added_by = :user_id
        ORDER BY e.id DESC
        '.($limit ? 'LIMIT '.$limit : ''),
        array(
            'user_id'=>$user_id
        )
    );

    foreach($exceptions as $exception) {
        echo '
    <tr>
        <td>',htmlspecialchars($exception['message']),'</td>
        <td>',date_time($exception['added']),'</td>
        <td><a href="',CONFIG_SITE_ADMIN_URL,'list_ip_log.php?ip=',htmlspecialchars($exception['user_ip']),'">',htmlspecialchars($exception['user_ip']),'</a></td>
        <td>',htmlspecialchars($exception['trace']),'</td>
    </tr>
    ';
    }

    echo '
      </tbody>
    </table>
     ';
}

function pager($baseurl, $max, $per_page, $current) {
    $lastchar = substr($baseurl, -1);

    if (strpos($baseurl, '?') && $lastchar != '?' && $lastchar != '&') {
        $baseurl .= '&amp;';
    } else {
        $baseurl .= '?';
    }

        $first_start = 0;
        $first_end = $first_start + $per_page*4;

        if ($current >= $first_end) {
            $first_end -= $per_page;
            $middle_start = $current - $per_page;
            $middle_end = $middle_start + $per_page*2;
        } else {
            $middle_start = 0;
            $middle_end = 0;
        }

        $last_start = $max - $per_page*2;
        $last_end = $max;

    echo '
    <div class="text-center">
        <ul class="pagination no-padding-or-margin">

        <li><a href="'.$baseurl.'from='.max(0, ($current-$per_page)).'">Prev</a></li>

        <li',(!$current ? ' class="active"' : ''),'><a href="',$baseurl,'">',min(1, $max),'-',min($max, $per_page),'</a></li>';

    $i = $per_page;
    while ($i < $max) {

        // are we in valid range to display buttons?
        if (
            !($i >= $first_start && $i <= $first_end)
            &&
            !($i >= $middle_start && $i <= $middle_end)
            &&
            !($i >= $last_start && $i <= $last_end)
        ) {
            $i+=$per_page;
            continue;
        }

        echo '<li',($current == $i ? ' class="active"' : ''),'><a href="',$baseurl,'from=',$i,'">', $i+1, ' - ', min($max, ($i+$per_page)), '</a></li>';

        $i+=$per_page;

        if ((
                (
                    ($i > $first_end) // if we've passed the first end
                    && // and
                    ($i - $per_page <= $first_end) // we've just crossed over the line
                    && // and
                    ($i - $per_page != $middle_start) // we're not adjacent to our middle start
                )
                || // or
                (
                    ($i > $middle_end) // if we've passed the current end
                    && // and
                    ($i - $per_page <= $middle_end) // we've just crossed over the line
                )
            ) && ($i + $per_page*3 < $max) // and we're more than three steps over from the last one
        ) {
            echo '<li><a>...</a></li>';
        }
    }

    echo '

        <li><a href="'.$baseurl.'from='.min($max-($max%$per_page), ($current+$per_page)).'">Next</a></li>

        </ul>
    </div>';
}