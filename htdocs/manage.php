<?php

define('IN_FILE', true);
require('../include/general.inc.php');

enforceAuthentication(CONFIG_UC_MODERATOR);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

}

head('Site management');
sectionHead('Site management');

if (!$_GET['view'] || $_GET['view'] == 'challenges') {

    managementMenu();

    sectionSubHead('CTF Overview');

    $cat_stmt = $db->query('SELECT * FROM categories ORDER BY title');
    while($category = $cat_stmt->fetch(PDO::FETCH_ASSOC)) {
        echo '<h4>',htmlspecialchars($category['title']), ' <a href="?view=edit_category&amp;id=',htmlspecialchars($category['id']),'"><img src="img/wrench.png" alt="Edit category" title="Edit category" /></a> <a href="?view=add_challenge&amp;id=',htmlspecialchars($category['id']),'"><img src="img/add.png" alt="Add challenge" title="Add challenge" /></a></h4>';

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

else if ($_GET['view'] == 'edit_challenge') {

    $stmt = $db->prepare('SELECT * FROM challenges WHERE id = :id');

    $stmt->execute(array(':id' => $_GET['id']));
    $challenge = $stmt->fetch(PDO::FETCH_ASSOC);

    sectionSubHead('Edit challenge: ' . $challenge['title']);

    echo '
    <form class="form-horizontal">

        <div class="control-group">
            <label class="control-label" for="title">Title</label>
            <div class="controls">
                <input type="text" id="title" class="input-block-level" placeholder="Title" value="',htmlspecialchars($challenge['title']),'">
            </div>
        </div>

        <div class="control-group">
            <label class="control-label" for="description">Description</label>
            <div class="controls">
                <textarea id="description" class="input-block-level" rows="10">',htmlspecialchars($challenge['description']),'</textarea>
            </div>
        </div>

        <div class="control-group">
            <label class="control-label" for="flag">Flag</label>
            <div class="controls">
                <input type="text" id="flag" class="input-block-level" placeholder="Flag" value="',htmlspecialchars($challenge['flag']),'">
            </div>
        </div>

        <div class="control-group">
            <label class="control-label" for="points">Points</label>
            <div class="controls">
                <input type="text" id="points" class="input-block-level" placeholder="Points" value="',htmlspecialchars($challenge['points']),'">
            </div>
        </div>';


        echo '
        <div class="control-group">
            <label class="control-label" for="category">Category</label>
            <div class="controls">

            <select id="category" name="category">';
        $stmt = $db->query('SELECT * FROM categories ORDER BY title');
        while ($category = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo '<option value="',htmlspecialchars($category['id']),'"',($category['id'] == $challenge['category'] ? ' selected="selected"' : ''),'>', htmlspecialchars($category['title']), '</option>';
        }
        echo '
            </select>

            </div>
        </div>
        ';


        echo '<div class="control-group">
            <label class="control-label" for="available_from">Available from</label>
            <div class="controls">
                <input type="text" id="available_from" class="input-block-level" placeholder="Available from" value="',htmlspecialchars($challenge['available_from']),'">
            </div>
        </div>

        <div class="control-group">
            <label class="control-label" for="available_until">Available until</label>
            <div class="controls">
                <input type="text" id="available_until" class="input-block-level" placeholder="Available until" value="',htmlspecialchars($challenge['available_until']),'">
            </div>
        </div>';

        echo '
            <div class="control-group">
                <label class="control-label" for="files">Files</label>
                <div class="controls">
                    <table id="files" class="table table-striped table-hover">
                      <thead>
                        <tr>
                          <th>Filename</th>
                          <th>Size</th>
                          <th>Description</th>
                          <th>Manage</th>
                        </tr>
                      </thead>
                      <tbody>
        ';

        echo '
                </tbody>
             </table>
             </div>
         </div>
        ';

        echo'
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Save changes</button>
            <button type="button" class="btn">Cancel</button>
        </div>

    </form>
    ';
}

foot();