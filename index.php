<?php

require_once('functions.php');
require_once('data.php');
// require_once('add.php');

// Ошибка 404
$page_404 = include_template('404.php', []);

// Контентная часть
$page_content = include_template('main.php', [
	'projects'					=> $projects,
	'tasks_list'				=> $tasks_list,
	'count_tasks'				=> $count_tasks,
	'projects_and_count_tasks'	=> $projects_and_count_tasks,
	'valid_id'					=> $valid_id,
	'show_complete_tasks' 		=> $show_complete_tasks,
	'page404' 					=> $page_404,
	'linkFile' 					=> $linkFile,
	'newFileName' 				=> $newFileName,
]);

// Шаблон страницы
$layout_content = include_template('layout.php', [
	'content'   =>  $page_content,
	'title'     => 'Дела в порядке',
	'user_name' => 'Константин'
]);

print($layout_content);
