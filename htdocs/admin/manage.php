<?php

require('../../include/mellivora.inc.php');

enforce_authentication(CONFIG_UC_MODERATOR);

head('Site management');

menu_management();

section_subhead('CTF Overview');

check_server_configuration();

$categories = db_query_fetch_all('SELECT * FROM categories ORDER BY title');
foreach($categories as $category) {
    echo '
    <h4>
    ',htmlspecialchars($category['title']), '
    <a href="edit_category.php?id=',htmlspecialchars($category['id']), '" class="btn btn-xs btn-primary">Edit category</a>
    <a href="new_challenge.php?category=',htmlspecialchars($category['id']),'" class="btn btn-xs btn-primary">Add challenge</a>
    </h4>
    ';

    echo '
    <table class="table table-striped table-hover">
      <thead>
        <tr>
          <th>Title</th>
          <th>Description</th>
          <th>Points</th>
          <th>Manage</th>
        </tr>
      </thead>
      <tbody>
    ';

    $challenges = db_select_all(
        'challenges',
        array(
            'id',
            'title',
            'description',
            'available_from',
            'available_until',
            'points'
        ),
        array('category' => $category['id']),
        'points ASC'
    );

    foreach($challenges as $challenge) {
        echo '
        <tr>
          <td>',htmlspecialchars($challenge['title']),'</td>
          <td>',htmlspecialchars(short_description($challenge['description'], 50)),'</td>
          <td>',number_format($challenge['points']), '</td>
          <td>
            <a href="edit_challenge.php?id=',htmlspecialchars($challenge['id']), '" class="btn btn-xs btn-primary">Edit</a>
            <a href="new_hint.php?id=',htmlspecialchars($challenge['id']),'" class="btn btn-xs btn-warning">Hint</a>
          </td>
        </tr>
        ';
    }
    echo '
        </tbody>
    </table>
    ';
}

foot();