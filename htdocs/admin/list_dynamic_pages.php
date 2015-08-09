<?php

require('../../include/mellivora.inc.php');

enforce_authentication(CONST_USER_CLASS_MODERATOR);

head('Dynamic pages');
menu_management();

section_head('Dynamic pages', button_link('New page', 'new_dynamic_page'), false);

$pages = db_select_all(
    'dynamic_pages',
    array(
        'id',
        'title',
        'visibility',
        'min_user_class'
    ),
    null,
    'title ASC'
);

echo '
    <table id="dynamic_pages" class="table table-striped table-hover">
      <thead>
        <tr>
          <th>Title</th>
          <th>visibility</th>
          <th>Min user class</th>
          <th>Manage</th>
        </tr>
      </thead>
      <tbody>
    ';

foreach($pages as $item) {
    echo '
    <tr>
        <td>',htmlspecialchars($item['title']),'</td>
        <td>',visibility_enum_to_name($item['visibility']), '</td>
        <td>',user_class_name($item['min_user_class']), '</td>
        <td><a href="'.CONFIG_SITE_ADMIN_URL.'edit_dynamic_page?id=',$item['id'],'" class="btn btn-xs btn-primary">Edit</a></td>
    </tr>
    ';
}

echo '
      </tbody>
    </table>
     ';

foot();