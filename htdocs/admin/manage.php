<?php

require('../include/mellivora.inc.php');

enforce_authentication(CONFIG_UC_MODERATOR);

head('Site management');

menu_management();

section_subhead('CTF Overview');

check_server_configuration();

$cat_stmt = $db->query('SELECT * FROM categories ORDER BY title');
while($category = $cat_stmt->fetch(PDO::FETCH_ASSOC)) {
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

    $stmt = $db->prepare('
    SELECT
    c.id,
    c.title,
    c.description,
    c.available_from,
    c.available_until,
    c.points
    FROM challenges AS c
    WHERE category = :category
    ORDER BY points ASC
');

    $stmt->execute(array(':category' => $category['id']));
    while($challenge = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo '
        <tr>
          <td>',htmlspecialchars($challenge['title']),'</td>
          <td>',htmlspecialchars(short_description($challenge['description'], 50)),'</td>
          <td>',number_format($challenge['points']), '</td>
          <td>
            <a href="admin/edit_challenge.php?id=',htmlspecialchars($challenge['id']), '" class="btn btn-xs btn-primary">Edit</a>
            <a href="admin/new_hint.php?id=',htmlspecialchars($challenge['id']),'" class="btn btn-xs btn-warning">Hint</a>
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