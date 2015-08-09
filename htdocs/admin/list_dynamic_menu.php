<?php

require('../../include/mellivora.inc.php');

enforce_authentication(CONST_USER_CLASS_MODERATOR);

head('Dynamic menu items');
menu_management();
section_head('Dynamic menu items', button_link('New menu item', 'new_dynamic_menu_item'), false);
$menu_items = db_query_fetch_all(
    'SELECT
        dm.id,
        dm.title,
        dm.permalink,
        dm.visibility,
        dm.min_user_class,
        dm.url,
        dc.title AS link_title
    FROM
        dynamic_menu AS dm
    LEFT JOIN
        dynamic_pages AS dc ON dc.id = dm.internal_page
    ORDER BY dm.title ASC'
);

echo '
    <table id="dynamic_menus" class="table table-striped table-hover">
      <thead>
        <tr>
          <th>Title</th>
          <th>Links to</th>
          <th>visibility</th>
          <th>Min user class</th>
          <th>Manage</th>
        </tr>
      </thead>
      <tbody>
    ';

foreach($menu_items as $item) {
    echo '
    <tr>
        <td>',htmlspecialchars($item['title']),'</td>
        <td>',
        ($item['link_title'] ?
                '<a href="'.CONFIG_SITE_URL.'content?show='.htmlspecialchars($item['permalink']).'">'.htmlspecialchars($item['link_title']).'</a>' :
                '<a href="'.htmlspecialchars($item['url']).'">'.short_description($item['url'], 20).'</a>'
        ),'
        </td>
        <td>',visibility_enum_to_name($item['visibility']), '</td>
        <td>',user_class_name($item['min_user_class']), '</td>
        <td><a href="'.CONFIG_SITE_ADMIN_URL.'edit_dynamic_menu_item?id=',$item['id'],'" class="btn btn-xs btn-primary">Edit</a></td>
    </tr>
    ';
}

echo '
      </tbody>
    </table>
     ';

foot();