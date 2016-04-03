<?php

require('../../include/mellivora.inc.php');

enforce_authentication(CONST_USER_CLASS_MODERATOR);

head('Site management');

menu_management();

check_server_configuration();

$categories = db_query_fetch_all('SELECT * FROM categories ORDER BY title');
if (empty($categories)) {
    message_generic('Welcome', 'Your CTF is looking a bit empty! Start by adding a category using the menu above.');
}

section_subhead('CTF Overview', '<a href="'.CONFIG_SITE_ADMIN_URL.'visualise">Visualise challenge availability</a>', false);

foreach($categories as $category) {
    echo '
    <h4>
    ',htmlspecialchars($category['title']),'
    <a href="edit_category.php?id=',htmlspecialchars($category['id']), '" class="btn btn-xs btn-primary">Edit category</a>
    <a href="new_challenge.php?category=',htmlspecialchars($category['id']),'" class="btn btn-xs btn-primary">Add challenge</a>
    </h4>
    ';

    $challenges = db_select_all(
        'challenges',
        array(
            'id',
            'title',
            'description',
            'exposed',
            'available_from',
            'available_until',
            'points'
        ),
        array('category' => $category['id']),
        'points ASC'
    );

    if (empty($challenges)) {
        message_inline_blue('This category is empty! Use the link above to add a challenge.');
    } else {

        echo '
    <table class="table table-striped table-hover">
      <thead>
        <tr>
          <th>Title</th>
          <th>Description</th>
          <th class="center">Points</th>
          <th class="center">Visibility</th>
          <th class="center">Manage</th>
        </tr>
      </thead>
      <tbody>
    ';

        foreach ($challenges as $challenge) {
            echo '
        <tr>
          <td>', htmlspecialchars($challenge['title']), '</td>
          <td>', htmlspecialchars(short_description($challenge['description'], 50)), '</td>
          <td class="center">', number_format($challenge['points']), '</td>
          <td class="center">

            ', get_availability_icons(
                $category['exposed'],
                $category['available_from'],
                $category['available_until'],
                'Category'
            ),'

            ', get_availability_icons(
                $challenge['exposed'],
                $challenge['available_from'],
                $challenge['available_until'],
                'Challenge'
            ),'

          </td>
          <td class="center">
            <a href="edit_challenge.php?id=', htmlspecialchars($challenge['id']), '" class="btn btn-xs btn-primary">Edit</a>
            <a href="new_hint.php?id=', htmlspecialchars($challenge['id']), '" class="btn btn-xs btn-warning">Hint</a>
          </td>
        </tr>
        ';
        }
        echo '
        </tbody>
    </table>
    ';
    }
}

foot();