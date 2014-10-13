<?php

require('../include/mellivora.inc.php');

login_session_refresh();

$menu_data = db_select_one(
    'dynamic_menu',
    array(
        'internal_page'
    ),
    array(
        'permalink'=>$_GET['show']
    )
);

if (!valid_id($menu_data['internal_page'])) {
    message_error("That isn't a valid link.");
}

$content = db_select_one(
    'dynamic_pages',
    array(
        'id',
        'title',
        'body',
        'visibility',
        'min_user_class'
    ),
    array(
        'id'=>$menu_data['internal_page']
    )
);

if ($content['visibility'] == 'private') {
    enforce_authentication($content['min_user_class']);
}

head($content['title']);

if (cache_start($content['id'], CONFIG_CACHE_TIME_DYNAMIC, CONST_DYNAMIC_PAGES_CACHE_GROUP)) {

    require(CONFIG_PATH_THIRDPARTY . 'nbbc/nbbc.php');

    $bbc = new BBCode();
    $bbc->SetEnableSmileys(false);

    echo $bbc->parse($content['body']);

    cache_end($content['id'], CONST_DYNAMIC_PAGES_CACHE_GROUP);
}

foot();