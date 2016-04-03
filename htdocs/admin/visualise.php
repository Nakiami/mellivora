<?php

require('../../include/mellivora.inc.php');

enforce_authentication(CONST_USER_CLASS_MODERATOR);

head('Visualise');

menu_management();

section_subhead('Visualise challenge availability', '<a href="'.CONFIG_SITE_ADMIN_URL.'">CTF Overview</a>', false);

echo '<script src="https://cdnjs.cloudflare.com/ajax/libs/vis/4.15.1/vis.min.js"></script>';
echo '<link href="https://cdnjs.cloudflare.com/ajax/libs/vis/4.15.1/vis.min.css" rel="stylesheet">';

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

    return
        !$challenge['parent_exposed'] ||
        !is_visible($challenge['parent_available_from'], $challenge['parent_available_until']);
}

function get_challenge_status_chars($category, $challenge) {
    $chars = "";

    if (!is_category_available($category) || is_parent_challenge_blocking($challenge)) {
        $chars .= unichr('2191');
    }

    if (!$challenge['exposed']) {
        $chars .= unichr('2718');
    }

    if (!is_visible($challenge['available_from'], $challenge['available_until'])) {
        $chars .= unichr('0231A');
    }

    return $chars;
}

function get_category_status_chars($category) {
    $chars = "";


    if (!$category['exposed']) {
        $chars .= unichr('2718');
    }

    if (!is_visible($category['available_from'], $category['available_until'])) {
        $chars .= unichr('0231A');
    }

    return $chars;
}

$nodes = array();
$edges = array();

$categories = db_query_fetch_all('SELECT * FROM categories ORDER BY title');

if (empty($categories)) {
    message_error('You need to add some categories and challenges before you can visualise!');
}

foreach($categories as $category) {
    $nodes[] = "{id: 'cat".$category['id']."', label: '".$category['title']."".get_category_status_chars($category)."', color: '".(is_category_available($category) ? "lime" : "red")."'}";

    $challenges = db_query_fetch_all(
        'SELECT
          c.id,
          c.title,
          c.description,
          c.exposed,
          c.available_from,
          c.available_until,
          c.relies_on,
          parent.exposed AS parent_exposed,
          parent.available_from AS parent_available_from,
          parent.available_until AS parent_available_until
        FROM
          challenges AS c
        LEFT JOIN challenges as parent ON parent.id = c.relies_on
        WHERE
          c.category = :category',
        array('category' => $category['id'])
    );

    foreach ($challenges as $challenge) {
        $nodes[] = "{id: 'chal".$challenge['id']."', label: '".$challenge['title']."".get_challenge_status_chars($category, $challenge)."', color: '".(is_challenge_available($category, $challenge) ? "lime" : "red")."'}";
        $edges[] = "{from: 'cat".$category['id']."', to: 'chal".$challenge['id']."'}";

        if ($challenge['relies_on']) {
            $edges[] = "{from: 'chal".$challenge['relies_on']."', to: 'chal".$challenge['id']."'}";
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
        layout: {
            hierarchical: {
                direction: 'UD'
            }
        }
    };

    // initialize your network!
    var network = new vis.Network(container, data, options);

</script>";

foot();