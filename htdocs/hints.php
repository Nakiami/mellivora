<?php

require('../include/mellivora.inc.php');

enforce_authentication();

head('Hints');

$cache = new Cache_Lite_Output(array('cacheDir'=>CONFIG_PATH_CACHE, 'lifeTime'=>CONFIG_CACHE_TIME_HINTS));
if (!($cache->start('hints'))) {

    $hints = db_query('
        SELECT
           h.id,
           h.added,
           h.body,
           c.title,
           ca.title AS category_title
        FROM hints AS h
        LEFT JOIN challenges AS c ON c.id = h.challenge
        LEFT JOIN categories AS ca ON ca.id = c.category
        WHERE c.available_from < UNIX_TIMESTAMP() AND c.available_until > UNIX_TIMESTAMP() AND h.visible = 1
        ORDER BY h.id DESC
    ');

    if (!sizeof($hints)) {
        message_generic("Hints", "No hints have been made available yet.", false);
    }

    section_head('Hints');

    echo '
        <table id="files" class="table table-striped table-hover">
          <thead>
            <tr>
              <th>Category</th>
              <th>Challenge</th>
              <th>Added</th>
              <th>Hint</th>
            </tr>
          </thead>
          <tbody>
        ';

    foreach ($hints as $hint) {
        echo '
        <tr>
            <td>',htmlspecialchars($hint['category_title']),'</td>
            <td>',htmlspecialchars($hint['title']),'</td>
            <td>',time_elapsed($hint['added']),' ago</td>
            <td>',htmlspecialchars($hint['body']),'</td>
        </tr>
        ';
    }

    echo '
          </tbody>
        </table>
         ';

    $cache->end();
}

foot();