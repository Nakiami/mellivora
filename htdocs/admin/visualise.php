<?php

require('../../include/mellivora.inc.php');

enforce_authentication(CONST_USER_CLASS_MODERATOR);

head('Visualise');

menu_management();

section_subhead('Visualise challenge availability', '<a href="'.CONFIG_SITE_ADMIN_URL.'">CTF Overview</a>', false);

echo '<script src="https://cdnjs.cloudflare.com/ajax/libs/vis/4.15.1/vis.min.js"></script>';
echo '<link href="https://cdnjs.cloudflare.com/ajax/libs/vis/4.15.1/vis.min.css" rel="stylesheet">';

message_inline_blue('Green = available now, Red = unavailable. ' . unichr(CONST_CHAR_CROSS) . ' = not exposed, ' . unichr(CONST_CHAR_CLOCK) . ' = not available because of time constraints, ' . unichr(CONST_CHAR_UPARROW) . ' = relies on challenge which is not yet solved, or is in a category which is not available');

echo '<div id="visualise-competition"></div>';

echo '<script type="text/javascript">';

function is_visible($from, $until) {
    $now = time();
    if ($now > $from && $now < $until) {
        return true;
    }
    return false;
}

function is_challenge_available($category, $challenge) {
    return
        $challenge['exposed'] &&
        is_visible($challenge['available_from'], $challenge['available_until']) &&
        is_category_available($category) &&
        !is_parent_challenge_blocking($challenge);
}

function is_category_available($category) {
    return
        $category['exposed'] &&
        is_visible($category['available_from'], $category['available_until']);
}

function is_parent_challenge_blocking($challenge) {
    if (!$challenge['relies_on']) {
        return false;
    }

    return !$challenge['parent_challenge_is_solved'];
}

function get_challenge_status_chars($category, $challenge) {
    $chars = "";

    if (!is_category_available($category) || is_parent_challenge_blocking($challenge)) {
        $chars .= unichr(CONST_CHAR_UPARROW);
    }

    if (!$challenge['exposed']) {
        $chars .= unichr(CONST_CHAR_CROSS);
    }

    if (!is_visible($challenge['available_from'], $challenge['available_until'])) {
        $chars .= unichr(CONST_CHAR_CLOCK);
    }

    return $chars;
}

function get_category_status_chars($category) {
    $chars = "";

    if (!$category['exposed']) {
        $chars .= unichr(CONST_CHAR_CROSS);
    }

    if (!is_visible($category['available_from'], $category['available_until'])) {
        $chars .= unichr(CONST_CHAR_CLOCK);
    }

    return $chars;
}

function whitespace_to_newline($string) {
    return str_replace(' ', '\n', $string);
}

$nodes = array();
$edges = array();

$categories = db_query_fetch_all('SELECT * FROM categories ORDER BY title');

if (empty($categories)) {
    message_error('You need to add some categories and challenges before you can visualise!');
}

foreach($categories as $category) {
    $nodes[] = "{id: 'cat".$category['id']."', label: '".whitespace_to_newline($category['title'])."".get_category_status_chars($category)."', color: '".(is_category_available($category) ? "lime" : "red")."', shape: 'circle'}";

    $challenges = db_query_fetch_all(
        'SELECT
          c.id,
          c.title,
          c.description,
          c.exposed,
          c.available_from,
          c.available_until,
          c.relies_on,
          (SELECT COUNT(*) FROM submissions AS s WHERE s.challenge = c.relies_on AND s.correct = 1) AS parent_challenge_is_solved
        FROM
          challenges AS c
        WHERE
          c.category = :category',
        array('category' => $category['id'])
    );

    foreach ($challenges as $challenge) {
        $nodes[] = "{id: 'chal".$challenge['id']."', group: ".$category['id'].", label: '".$challenge['title']."".get_challenge_status_chars($category, $challenge)."', color: '".(is_challenge_available($category, $challenge) ? "lime" : "red")."'}";
        $edges[] = "{from: 'cat".$category['id']."', to: 'chal".$challenge['id']."', dashes: true}";

        if ($challenge['relies_on']) {
            $edges[] = "{from: 'chal".$challenge['relies_on']."', to: 'chal".$challenge['id']."', arrows:'to', color: '".(is_parent_challenge_blocking($challenge) ? "red" : "green")."'}";
        }
    }
}

echo '
// create an array with nodes
var nodes = new vis.DataSet([
    '.implode(',', $nodes).'
]);';

echo '
// create an array with edges
var edges = new vis.DataSet([
    '.implode(',', $edges).'
])
';

echo "
    // create a network
    var container = document.getElementById('visualise-competition');

    // provide the data in the vis format
    var data = {
        nodes: nodes,
        edges: edges
    };
    var options = {
        nodes: {
            shadow: true,
            shape: 'box'
        },
        edges: {
            shadow: false
        }
    };

    // initialize your network!
    var network = new vis.Network(container, data, options);

</script>";

foot();