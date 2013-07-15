<?php

define('IN_FILE', true);
require('../include/general.inc.php');

enforceAuthentication(CONFIG_UC_MODERATOR);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($_POST['action'] == 'submit_flag') {

        // make sure user isn't "accidentally" submitting a correct flag twice
        $stmt = $db->prepare('SELECT * FROM submissions WHERE user = :user AND challenge = :challenge AND correct = 1');
        $stmt->execute(array(':user' => $_SESSION['id'], ':challenge' => $_POST['challenge']));

        if ($stmt->rowCount()) {
            errorMessage('You may only submit a correct flag once :p');
        }

        // get challenge information
        $stmt = $db->prepare('SELECT flag FROM challenges WHERE id = :challenge');
        $stmt->execute(array(':challenge' => $_POST['challenge']));
        $challenge = $stmt->fetch(PDO::FETCH_ASSOC);

        $correct = false;
        if ($_POST['flag'] == $challenge['flag']) {
            $correct = true;
        }

        // insert submission
        $stmt = $db->prepare('INSERT INTO submissions (challenge, user, flag, correct) VALUES (:challenge, :user, :flag, :correct)');
        $stmt->execute(array(':user' => $_SESSION['id'], ':flag' => $_POST['flag'], ':challenge' => $_POST['challenge'], ':correct' => $correct));

        header('location: challenges?success=' . ($correct ? '1' : '0'));
    }

    exit();
}

head('Site management');
sectionHead('Site management');

echo '
<div class="btn-group">
    <button class="btn btn-warning dropdown-toggle" data-toggle="dropdown">Categories <span class="caret"></span></button>
    <ul class="dropdown-menu">
      <li><a href="#">Add category</a></li>
      <li><a href="#">List categories</a></li>
    </ul>
</div><!-- /btn-group -->

<div class="btn-group">
    <button class="btn btn-warning dropdown-toggle" data-toggle="dropdown">Challenges <span class="caret"></span></button>
    <ul class="dropdown-menu">
      <li><a href="#">Add challenge</a></li>
      <li><a href="#">List challenges</a></li>
    </ul>
</div><!-- /btn-group -->

<div class="btn-group">
    <button class="btn btn-warning dropdown-toggle" data-toggle="dropdown">Submissions <span class="caret"></span></button>
    <ul class="dropdown-menu">
      <li><a href="#">List submissions</a></li>
    </ul>
</div><!-- /btn-group -->


<div class="btn-group">
    <button class="btn btn-warning dropdown-toggle" data-toggle="dropdown">Users <span class="caret"></span></button>
    <ul class="dropdown-menu">
      <li><a href="#">Add user</a></li>
      <li><a href="#">List users</a></li>
      <li><a href="#">Search users</a></li>
    </ul>
</div><!-- /btn-group -->
';

if (!$_GET['view'] || $_GET['view'] == 'challenges') {

    sectionSubHead('Overview');

    $cat_stmt = $db->query('SELECT * FROM categories ORDER BY title');
    while($category = $cat_stmt->fetch(PDO::FETCH_ASSOC)) {
        echo '<h4>',htmlspecialchars($category['title']), ' <a href="?view=edit_category&amp;id=',htmlspecialchars($category['id']),'"><img src="img/wrench.png" alt="Edit category" title="Edit category" /></a> <a href="?view=add_challenge&amp;id=',htmlspecialchars($category['id']),'"><img src="img/add.png" alt="Add challenge" title="Add challenge" /></a></h4>';

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
        while($challenge = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo '
            <tr>
              <td>',htmlspecialchars($challenge['title']),'</td>
              <td>',htmlspecialchars(shortDescription($challenge['description'], 50)),'</td>
              <td>',number_format($challenge['points']),'</td>
              <td><a href="?view=edit_challenge&amp;id=',htmlspecialchars($challenge['id']),'"><img src="img/wrench_orange.png" alt="Edit challenge" title="Edit challenge" /></a></td>
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