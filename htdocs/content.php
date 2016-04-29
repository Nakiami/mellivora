<?php

require('../include/mellivora.inc.php');

login_session_refresh();

if (!isset($_GET['show'])) {
    message_error(lang_get('please_request_page'));
}

$menu_data = db_select_one(
    'dynamic_menu',
    array(
        'internal_page'
    ),
    array(
        'permalink'=>$_GET['show']
    )
);

if (!is_valid_id($menu_data['internal_page'])) {
    message_error(lang_get('not_a_valid_link'));
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

if (cache_start($content['id'], CONFIG_CACHE_TIME_DYNAMIC, CONST_CACHE_DYNAMIC_PAGES_GROUP)) {

    section_head($content['title']);

    echo get_bbcode()->parse($content['body']);

    cache_end($content['id'], CONST_CACHE_DYNAMIC_PAGES_GROUP);
}

foot();