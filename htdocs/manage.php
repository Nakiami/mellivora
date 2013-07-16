<?php

define('IN_FILE', true);
require('../include/general.inc.php');

enforceAuthentication(CONFIG_UC_MODERATOR);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

}

head('Site management');
sectionHead('Site management');

managementMenu();

sectionSubHead('CTF Overview');

$cat_stmt = $db->query('SELECT * FROM categories ORDER BY title');
while($category = $cat_stmt->fetch(PDO::FETCH_ASSOC)) {
    echo '
    <h4>
    ',htmlspecialchars($category['title']), '
    <a href="edit_category.php?id=',htmlspecialchars($category['id']),'"><img src="img/wrench.png" alt="Edit category" title="Edit category" /></a>
    <a href="new_challenge.php?category=',htmlspecialchars($category['id']),'"><img src="img/add.png" alt="Add challenge" title="Add challenge" /></a>
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
    c.points,
    s.correct
    FROM challenges AS c
    LEFT JOIN submissions AS s ON c.id = s.challenge AND s.user = :user AND correct = 1
    WHERE category = :category
    ORDER BY points ASC
');

    $stmt->execute(array(':user' => $_SESSION['id'], ':category' => $category['id']));
    while($challenge = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo '
        <tr>
          <td>',htmlspecialchars($challenge['title']),'</td>
          <td>',htmlspecialchars(shortDescription($challenge['description'], 50)),'</td>
          <td>',number_format($challenge['points']), '</td>
          <td>
            <a href="edit_challenge.php?id=',htmlspecialchars($challenge['id']),'"><img src="img/wrench_orange.png" alt="Edit challenge" title="Edit challenge" /></a>
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