<?php

require_once('functions.php');
require_once('data.php');

$page_404 = include_template('404.php', []);

$page_content = include_template('main.php', [
	'categories' 			=> $categories,
	'task_list' 			=> $task_list,
	'count_task' 			=> $count_task,
	'show_complete_tasks' 	=> $show_complete_tasks,
	'page404' 				=> $page_404,
	'tasks_id' 				=> $tasks_id,
]);

$layout_content = include_template('layout.php', [
	'content'   =>  $page_content,
	'title'     => 'Дела в порядке',
	'user_name' => 'Константин',
]);

print($layout_content);
