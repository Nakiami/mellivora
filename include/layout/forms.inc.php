<?php

function form_start($action='', $class='', $enctype='') {
    echo '
    <form method="post" class="',($class ? $class : 'form-horizontal'),'"',($enctype ? ' enctype="'.$enctype.'"' : ''),'',($action ? ' action="'.CONFIG_SITE_URL.$action.'"' : ''),' role="form">
    ';

    form_xsrf_token();
}

function form_end() {
    echo '</form>';
}

function form_hidden ($name, $value) {
    $name = htmlspecialchars($name);
    $field_name = strtolower(str_replace(' ','_',$name));
    echo '<input type="hidden" name="',$field_name,'" value="',htmlspecialchars($value),'" />';
}

function form_xsrf_token() {
    echo '<input type="hidden" name="xsrf_token" value="',htmlspecialchars($_SESSION['xsrf_token']),'" />';
}

function form_file ($name) {
    $name = htmlspecialchars($name);
    $field_name = strtolower(str_replace(' ','_',$name));
    echo '<input type="file" name="',$field_name,'" id="',$field_name,'" />';
}

function form_input_text($name, $prefill = false, array $options = null) {
    $name = htmlspecialchars($name);
    $field_name = strtolower(str_replace(' ','_',$name));
    echo '
    <div class="form-group">
      <label class="col-sm-2 control-label" for="',$field_name,'">',$name,'</label>
      <div class="col-sm-10">
          <input
            type="text"
            id="',$field_name,'"
            name="',$field_name,'"
            class="form-control"
            placeholder="',$name,'"
            ',($prefill !== false ? ' value="'.htmlspecialchars($prefill).'"' : ''),'
            ',(array_get($options, 'disabled') ? ' disabled' : ''),'
            ',(array_get($options, 'autocomplete') ? ' autocomplete="'.$options['autocomplete'].'"' : ''),'
            ',(array_get($options, 'autofocus') ? ' autofocus' : ''),'
          />
      </div>
    </div>
    ';
}

function form_input_password($name, $prefill = false, array $options = null) {
    $name = htmlspecialchars($name);
    $field_name = strtolower(str_replace(' ','_',$name));
    echo '
    <div class="form-group">
      <label class="col-sm-2 control-label" for="',$field_name,'">',$name,'</label>
      <div class="col-sm-10">
          <input type="password" id="',$field_name,'" name="',$field_name,'" class="form-control" placeholder="',$name,'"',($prefill !== false ? ' value="'.htmlspecialchars($prefill).'"' : ''),'',($options['disabled'] ? ' disabled' : ''),' required />
      </div>
    </div>
    ';
}

function form_input_captcha($position = 'private') {

    if (($position == 'private' && CONFIG_RECAPTCHA_ENABLE_PRIVATE) || ($position == 'public' && CONFIG_RECAPTCHA_ENABLE_PUBLIC)) {
        echo '
        <div class="form-group">
          <label class="col-sm-2 control-label" for="captcha"></label>
          <div class="col-sm-10">';

        display_captcha();

        echo '</div>
        </div>
        ';
    }
}

function form_input_checkbox ($name, $checked = 0) {
    $name = htmlspecialchars($name);
    $field_name = strtolower(str_replace(' ','_',$name));
    echo '
    <div class="form-group">
      <label class="col-sm-2 control-label" for="',$field_name,'">',$name,'</label>
      <div class="col-sm-10">
          <input type="checkbox" id="',$field_name,'" name="',$field_name,'" value="1"',($checked ? ' checked="checked"' : ''),' />
      </div>
    </div>
    ';
}

function form_generic ($name, $generic) {
    $name = htmlspecialchars($name);
    $field_name = strtolower(str_replace(' ','_',$name));
    echo '
    <div class="form-group">
      <label class="col-sm-2 control-label" for="',$field_name,'">',$name,'</label>
      <div class="col-sm-10">
          ',$generic,'
      </div>
    </div>
    ';
}

function form_textarea($name, $prefill = false) {
    $name = htmlspecialchars($name);
    $field_name = strtolower(str_replace(' ','_',$name));
    echo '
    <div class="form-group">
      <label class="col-sm-2 control-label" for="',$field_name,'">',$name,'</label>
      <div class="col-sm-10">
          <textarea id="',$field_name,'" name="',$field_name,'" class="form-control" rows="10">',($prefill !== false ? htmlspecialchars($prefill) : ''),'</textarea>
      </div>
    </div>
    ';
}

function form_button_submit ($name, $type = 'primary') {
    $name = htmlspecialchars($name);
    $field_name = strtolower(str_replace(' ','_',$name));
    echo '
    <div class="form-group">
      <label class="col-sm-2 control-label" for="',$field_name,'"></label>
      <div class="col-sm-10">
          <button type="submit" id="',$field_name,'" class="btn btn-',htmlspecialchars($type),'">',$name,'</button>
      </div>
    </div>
    ';
}

function form_select ($opts, $name, $value, $selected, $option, $optgroup='') {
    $name = htmlspecialchars($name);
    $field_name = strtolower(str_replace(' ','_',$name));
    echo '
    <div class="form-group">
        <label class="col-sm-2 control-label" for="',$field_name,'">',$name,'</label>
        <div class="col-sm-10">

        <select id="',$field_name,'" name="',$field_name,'">';

    $group = '';
    foreach ($opts as $opt) {

        if ($optgroup && $group != $opt[$optgroup]) {
            if ($group) {
                echo '</optgroup>';
            }
            echo '<optgroup label="',htmlspecialchars($opt[$optgroup]),'">';
        }

        echo '<option value="',htmlspecialchars($opt[$value]),'"',($opt[$value] == $selected ? ' selected="selected"' : ''),'>', htmlspecialchars($opt[$option]), '</option>';

        if ($optgroup) {
            $group = $opt[$optgroup];
        }
    }

    if ($optgroup) {
        echo '</optgroup>';
    }

    echo '
        </select>

        </div>
    </div>
    ';
}

function form_bbcode_manual() {
    echo '
    <div class="form-group">
      <label class="col-sm-2 control-label" for="bbcode">BBcode</label>
      <div class="col-sm-10">';
    bbcode_manual();
    echo '
      </div>
    </div>
    ';
}

function country_select() {
    $countries = db_select_all(
        'countries',
        array(
            'id',
            'country_name'
        ),
        null,
        'country_name ASC'
    );

    echo '<select name="country" class="form-control" required="required">
            <option disabled selected>-- Please select a country --</option>';

    foreach ($countries as $country) {
        echo '<option value="',htmlspecialchars($country['id']),'">',htmlspecialchars($country['country_name']),'</option>';
    }

    echo '</select>';
}

function dynamic_visibility_select($selected = null) {
    $options = array(
        array(
            'val'=>CONST_DYNAMIC_VISIBILITY_BOTH,
            'opt'=>visibility_enum_to_name(CONST_DYNAMIC_VISIBILITY_BOTH)
        ),
        array(
            'val'=>CONST_DYNAMIC_VISIBILITY_PRIVATE,
            'opt'=>visibility_enum_to_name(CONST_DYNAMIC_VISIBILITY_PRIVATE)
        ),
        array(
            'val'=>CONST_DYNAMIC_VISIBILITY_PUBLIC,
            'opt'=>visibility_enum_to_name(CONST_DYNAMIC_VISIBILITY_PUBLIC)
        )
    );

    form_select($options, 'Visibility', 'val', $selected, 'opt');
}

function user_class_select($selected = null) {
    $options = array(
        array(
            'val'=>CONFIG_UC_USER,
            'opt'=>user_class_name(CONFIG_UC_USER)
        ),
        array(
            'val'=>CONFIG_UC_MODERATOR,
            'opt'=>user_class_name(CONFIG_UC_MODERATOR)
        )
    );

    form_select($options, 'Min user class', 'val', $selected, 'opt');
}