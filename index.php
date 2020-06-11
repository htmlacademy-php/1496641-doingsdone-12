<?php

require_once('functions.php');
require_once('data.php');

$page_content = include_template('main.php', [
	'categories' => $categories,
	'task_list' => $task_list,
	'tasks_list' => $tasks_list,
	'show_complete_tasks' => $show_complete_tasks
]);

$layout_content = include_template('layout.php', [
	'content'   => $page_content,
	'title'     => 'Дела в порядке',
	'user_name' => 'Константин'
]);

print($layout_content);
