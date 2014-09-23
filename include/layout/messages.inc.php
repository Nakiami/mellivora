<?php

function message_error ($message, $head = true, $foot = true, $exit = true) {
    if ($head) {
        head('Error');
    }

    section_subhead('Error');

    message_inline_red($message);

    if ($foot) {
        foot();
    }

    if ($exit) {
        exit();
    }
}

function message_generic ($title, $message, $head = true, $foot = true, $exit = true) {
    if ($head) {
        head($title);
    }

    section_subhead($title);

    message_inline_blue($message);

    if ($foot) {
        foot();
    }

    if ($exit) {
        exit();
    }
}

function message_inline_bland ($message) {
    echo '<p>',htmlspecialchars($message),'</p>';
}

function message_inline_blue ($message, $strip_html = true) {
    echo '<div class="alert alert-info">',($strip_html ? htmlspecialchars($message) : $message),'</div>';
}

function message_inline_red ($message, $strip_html = true) {
    echo '<div class="alert alert-danger">',($strip_html ? htmlspecialchars($message) : $message),'</div>';
}

function message_inline_yellow ($message, $strip_html = true) {
    echo '<div class="alert alert-warning">',($strip_html ? htmlspecialchars($message) : $message),'</div>';
}

function message_inline_green ($message, $strip_html = true) {
    echo '<div class="alert alert-success">',($strip_html ? htmlspecialchars($message) : $message),'</div>';
}