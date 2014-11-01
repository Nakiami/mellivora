<?php

function dynamic_menu_content() {
    $cache_name = user_is_logged_in() ? 'logged_in_'.$_SESSION['class'] : 'guest';

    if (cache_start($cache_name, CONFIG_CACHE_TIME_DYNAMIC, CONST_DYNAMIC_MENU_CACHE_GROUP)) {
        $entries = db_query_fetch_all(
            'SELECT
                title,
                internal_page,
                permalink,
                url,
                visibility
            FROM
                dynamic_menu
            WHERE
                '.(user_is_logged_in() ?
                    'min_user_class <= '.$_SESSION['class'].' AND (visibility = "private" OR visibility = "both")' :
                    'visibility = "public" OR visibility = "both"'
            ).'
            ORDER BY priority DESC'
        );

        foreach($entries as $entry) {
            echo '
            <li>
                <a href="',($entry['internal_page'] ? CONFIG_SITE_URL.'content?show='.$entry['permalink'] : htmlspecialchars($entry['url'])),'">',htmlspecialchars($entry['title']),'</a>
            </li>
            ';
        }

        cache_end($cache_name, CONST_DYNAMIC_MENU_CACHE_GROUP);
    }
}