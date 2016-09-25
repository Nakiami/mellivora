<?php
require(CONST_PATH_LAYOUT . 'login_dialog.inc.php');
require(CONST_PATH_LAYOUT . 'messages.inc.php');
require(CONST_PATH_LAYOUT . 'scores.inc.php');
require(CONST_PATH_LAYOUT . 'user.inc.php');
require(CONST_PATH_LAYOUT . 'forms.inc.php');
require(CONST_PATH_LAYOUT . 'challenges.inc.php');
require(CONST_PATH_LAYOUT . 'dynamic.inc.php');

// set global head_sent variable
$head_sent = false;
// singleton bbcode instance
$bbc = null;

function head($title = '') {
    global $head_sent;

    header('Content-Type: text/html; charset=utf-8');
    echo '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>',($title ? htmlspecialchars($title) . ' : ' : '') , CONFIG_SITE_NAME, ' - ', CONFIG_SITE_SLOGAN,'</title>
    <meta name="description" content="',CONFIG_SITE_DESCRIPTION,'">
    <meta name="author" content="">
    <link rel="icon" href="',CONFIG_SITE_URL_STATIC_RESOURCES,'img/favicon.png" type="image/png" />

    <!-- CSS -->
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css" rel="stylesheet">
    <link href="',CONFIG_SITE_URL_STATIC_RESOURCES,'css/mellivora.css" rel="stylesheet">';

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
<div class="page">
    <nav class="header" id="header">
        <div id="header-inner">
            <div id="header-logo">
                <a href="',CONFIG_SITE_URL,'">
                    <h3 id="site-logo-text">',CONFIG_SITE_NAME,'</h3>
                    <div id="site-logo">
                        <object data="'.CONFIG_SITE_URL_STATIC_RESOURCES.'img/mellivora.svg" type="image/svg+xml"></object>
                    </div>
                </a>
            </div>
            <div id="header-menu">
                <ul class="nav nav-pills pull-right" id="menu-main">';

                    if (user_is_logged_in()) {

                        if (user_is_staff()) {
                            echo '<li><a href="',CONFIG_SITE_ADMIN_URL,'">',lang_get('manage'),'</a></li>';
                        }

                        echo '
                            <li><a href="',CONFIG_SITE_URL,'home">',lang_get('home'),'</a></li>
                            <li><a href="',CONFIG_SITE_URL,'challenges">',lang_get('challenges'),'</a></li>
                            <li><a href="',CONFIG_SITE_URL,'hints">',lang_get('hints'),'</a></li>
                            <li><a href="',CONFIG_SITE_URL,'scores">',lang_get('scores'),'</a></li>
                            <li><a href="',CONFIG_SITE_URL,'profile">',lang_get('profile'),'</a></li>
                            ',dynamic_menu_content(),'
                            <li>',form_logout(),'</li>
                            ';

                    } else {
                        echo '
                            <li><a href="',CONFIG_SITE_URL,'home">',lang_get('home'),'</a></li>
                            <li><a href="',CONFIG_SITE_URL,'scores">',lang_get('scoreboard'),'</a></li>
                            ',dynamic_menu_content(),'
                            <li><a href="',CONFIG_SITE_URL,'register">',lang_get('register'),'</a></li>
                            <li><a href="" data-toggle="modal" data-target="#login-dialog">',lang_get('log_in'),'</a></li>
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
        message_inline_green('<h3>'.lang_get('action_success').'</h3>', false);
    } else if (isset($_GET['generic_failure'])) {
        message_inline_red('<h3>'.lang_get('action_failure').'</h3>', false);
    } else if (isset($_GET['generic_warning'])) {
        message_inline_red('<h3>'.lang_get('action_something_went_wrong').'</h3>', false);
    }

    $head_sent = true;
}

function foot () {
    echo '

    </div> <!-- / content container -->

</div> <!-- /container -->

<div id="footer">
    <div class="fade">
        <div id="footer-logo"/>
            <object data="'.CONFIG_SITE_URL_STATIC_RESOURCES.'img/mellivora.svg" type="image/svg+xml"></object>
        </div>
        <p>Powered by <a href="https://github.com/Nakiami/mellivora">Mellivora</a></p>
    </div>
</div>

</div> <!-- /page -->

<!-- JS -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
<script src="https://netdna.bootstrapcdn.com/bootstrap/3.1.1/js/bootstrap.min.js"></script>
<script type="text/javascript" src="',CONFIG_SITE_URL_STATIC_RESOURCES,'js/mellivora.js"></script>

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
        <button class="btn btn-warning dropdown-toggle btn-xs" data-toggle="dropdown">', lang_get('news'), ' <span class="caret"></span></button>
        <ul class="dropdown-menu">
          <li><a href="',CONFIG_SITE_ADMIN_URL,'new_news" id="ssssssd">', lang_get('add_news_item'), '</a></li>
          <li><a href="',CONFIG_SITE_ADMIN_URL,'list_news">', lang_get('list_news_item'), '</a></li>
        </ul>
    </div>

    <div class="btn-group">
        <button class="btn btn-warning dropdown-toggle btn-xs" data-toggle="dropdown">', lang_get('categories'), ' <span class="caret"></span></button>
        <ul class="dropdown-menu">
          <li><a href="',CONFIG_SITE_ADMIN_URL,'new_category">', lang_get('add_category'), '</a></li>
          <li><a href="',CONFIG_SITE_ADMIN_URL,'">', lang_get('list_categories'), '</a></li>
        </ul>
    </div>

    <div class="btn-group">
        <button class="btn btn-warning dropdown-toggle btn-xs" data-toggle="dropdown">', lang_get('challenges'), ' <span class="caret"></span></button>
        <ul class="dropdown-menu">
          <li><a href="',CONFIG_SITE_ADMIN_URL,'new_challenge">', lang_get('add_challenge'), '</a></li>
          <li><a href="',CONFIG_SITE_ADMIN_URL,'">', lang_get('list_challenges'), '</a></li>
        </ul>
    </div>

    <div class="btn-group">
        <button class="btn btn-warning dropdown-toggle btn-xs" data-toggle="dropdown">', lang_get('submissions'), ' <span class="caret"></span></button>
        <ul class="dropdown-menu">
          <li><a href="',CONFIG_SITE_ADMIN_URL,'list_submissions?only_needing_marking=1">', lang_get('list_submissions_in_need_of_marking'), '</a></li>
          <li><a href="',CONFIG_SITE_ADMIN_URL,'list_submissions">', lang_get('list_all_submissions'), '</a></li>
        </ul>
    </div>


    <div class="btn-group">
        <button class="btn btn-warning dropdown-toggle btn-xs" data-toggle="dropdown">', lang_get('users'), '  <span class="caret"></span></button>
        <ul class="dropdown-menu">
          <li role="presentation" class="dropdown-header">', lang_get('users'), ' </li>
          <li><a href="',CONFIG_SITE_ADMIN_URL,'list_users">', lang_get('list_users'), ' </a></li>
          <li role="presentation" class="dropdown-header">', lang_get('user_types'), ' </li>
          <li><a href="',CONFIG_SITE_ADMIN_URL,'new_user_type">', lang_get('add_user_type'), ' </a></li>
          <li><a href="',CONFIG_SITE_ADMIN_URL,'list_user_types">', lang_get('list_user_types'), ' </a></li>
        </ul>
    </div>

    <div class="btn-group">
        <button class="btn btn-warning dropdown-toggle btn-xs" data-toggle="dropdown">', lang_get('signup_rules'), ' <span class="caret"></span></button>
        <ul class="dropdown-menu">
          <li><a href="',CONFIG_SITE_ADMIN_URL,'new_restrict_email">', lang_get('new_rule'), '</a></li>
          <li><a href="',CONFIG_SITE_ADMIN_URL,'list_restrict_email">', lang_get('list_rules'), '</a></li>
          <li><a href="',CONFIG_SITE_ADMIN_URL,'test_restrict_email">', lang_get('test_rule'), '</a></li>
        </ul>
    </div>

    <div class="btn-group">
        <button class="btn btn-warning dropdown-toggle btn-xs" data-toggle="dropdown">Email <span class="caret"></span></button>
        <ul class="dropdown-menu">
          <li><a href="',CONFIG_SITE_ADMIN_URL,'new_email">', lang_get('single_email'), '</a></li>
          <li><a href="',CONFIG_SITE_ADMIN_URL,'new_email?bcc=all">', lang_get('email_all_users'), '</a></li>
        </ul>
    </div>

    <div class="btn-group">
        <button class="btn btn-warning dropdown-toggle btn-xs" data-toggle="dropdown">', lang_get('hints'), ' <span class="caret"></span></button>
        <ul class="dropdown-menu">
          <li><a href="',CONFIG_SITE_ADMIN_URL,'new_hint">', lang_get('new_hint'), '</a></li>
          <li><a href="',CONFIG_SITE_ADMIN_URL,'list_hints">', lang_get('list_hints'), '</a></li>
        </ul>
    </div>

    <div class="btn-group">
        <button class="btn btn-warning dropdown-toggle btn-xs" data-toggle="dropdown"> ', lang_get('dynamic_content'), '<span class="caret"></span></button>
        <ul class="dropdown-menu">
          <li role="presentation" class="dropdown-header">', lang_get('menu'), '</li>
          <li><a href="',CONFIG_SITE_ADMIN_URL,'new_dynamic_menu_item">', lang_get('new_menu_item'), '</a></li>
          <li><a href="',CONFIG_SITE_ADMIN_URL,'list_dynamic_menu">', lang_get('list_menu_items'), '</a></li>
          <li role="presentation" class="dropdown-header">', lang_get('pages'), '</li>
          <li><a href="',CONFIG_SITE_ADMIN_URL,'new_dynamic_page">', lang_get('new_page'), '</a></li>
          <li><a href="',CONFIG_SITE_ADMIN_URL,'list_dynamic_pages">', lang_get('list_pages'), '</a></li>
        </ul>
    </div>

    <div class="btn-group">
        <button class="btn btn-warning dropdown-toggle btn-xs" data-toggle="dropdown">', lang_get('exceptions'), ' <span class="caret"></span></button>
        <ul class="dropdown-menu">
          <li><a href="',CONFIG_SITE_ADMIN_URL,'list_exceptions">', lang_get('list_exceptions'), '</a></li>
          <li><a href="',CONFIG_SITE_ADMIN_URL,'edit_exceptions">', lang_get('clear_exceptions'), '</a></li>
        </ul>
    </div>
    
    <div class="btn-group">
        <button class="btn btn-warning dropdown-toggle btn-xs" data-toggle="dropdown">', lang_get('search'), ' <span class="caret"></span></button>
        <ul class="dropdown-menu">
          <li><a href="',CONFIG_SITE_ADMIN_URL,'search">', lang_get('search'), '</a></li>
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
        <img src="'.CONFIG_SITE_URL_STATIC_RESOURCES.'img/flags/'.$country_code.'.png" class="has-tooltip" data-toggle="tooltip" data-placement="right" alt="'.$country_code.'" title="'.$country_name.'" />
    </a>';

    if ($return) {
        return $flag_link;
    }

    echo $flag_link;
}

function pager_filter_from_get($get) {
    if (array_get($get, 'from') != null) {
        unset($get['from']);
    }
    return http_build_query($get);
}

function pager($base_url, $max, $per_page, $current) {
    if (isset($current)){
        validate_integer($current);
    }

    // by default, we add on any get parameter to the pager link
    $get_argument_string = pager_filter_from_get($_GET);
    if (!empty($get_argument_string)) {
        $base_url .= pager_url_param_joining_char($base_url) . $get_argument_string;
    }

    $base_url .= pager_url_param_joining_char($base_url);

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

        <li><a href="'.htmlspecialchars($base_url).'from='.max(0, ($current-$per_page)).'">Prev</a></li>

        <li',(!$current ? ' class="active"' : ''),'><a href="',htmlspecialchars($base_url),'">',min(1, $max),'-',min($max, $per_page),'</a></li>';

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

        echo '<li',($current == $i ? ' class="active"' : ''),'><a href="',htmlspecialchars($base_url),'from=',$i,'">', $i+1, ' - ', min($max, ($i+$per_page)), '</a></li>';

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

        <li><a href="'.htmlspecialchars($base_url).'from='.min($max-($max%$per_page), ($current+$per_page)).'">Next</a></li>

        </ul>
    </div>';
}

function pager_url_param_joining_char($base_url) {
    $last_char = substr($base_url, -1);
    if (strpos($base_url, '?') && $last_char != '?' && $last_char != '&') {
        return '&';
    } else {
        return '?';
    }
}

function get_pager_from($val) {
    if (is_valid_id(array_get($val, 'from'))) {
        return $val['from'];
    }

    return 0;
}

function get_availability_icons($exposed, $available_from, $available_until, $item_name) {
    $icons = "";

    if (!$exposed) {
        $icons .= '<span class="glyphicon glyphicon-ban-circle has-tooltip" data-toggle="tooltip" data-placement="top" title="'. htmlspecialchars($item_name) .' not exposed"></span> ';
    }

    if (!is_item_available($available_from, $available_until)) {
        $icons .= '<span class="glyphicon glyphicon-eye-close has-tooltip" data-toggle="tooltip" data-placement="top" title="'. htmlspecialchars($item_name) .' not available"></span> ';
    }

    if ($exposed && is_item_available($available_from, $available_until)) {
        $icons .= '<span class="glyphicon glyphicon-eye-open has-tooltip" data-toggle="tooltip" data-placement="top" title="'. htmlspecialchars($item_name) .' exposed and available"></span> ';
    }

    return $icons;
}

function get_bbcode() {
    global $bbc;

    if ($bbc === null) {
        $bbc = new BBCode();
        $bbc->SetEnableSmileys(false);
    }

    return $bbc;
}
