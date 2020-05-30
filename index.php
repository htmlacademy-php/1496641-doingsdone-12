<?php

require_once('functions.php');
require_once('data.php');

$page_content = include_template('main.php', [
    'categories' => $categories,
    'tasks_list' => $tasks_list
]);

$layout_content = include_template('layout.php', [
    'content'   => $page_content,
    'title'     => 'Дела в порядке',
    'user_name' => 'Константин'
]);

print($layout_content);
