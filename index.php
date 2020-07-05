<?php

require_once('functions.php');
require_once('data.php');

// Ошибка 404
$page_404 = include_template('404.php', []);

// Шаблон для гостя
$guest = include_template('guest.php', []);

// Контентная часть
$page_content = include_template('main.php', [
	'projects'					=> $projects,
	'tasks_list'				=> $tasks_list,
	'count_tasks'				=> $count_tasks,
	'projects_and_count_tasks'	=> $projects_and_count_tasks,
	'valid_id'					=> $valid_id,
	'show_complete_tasks' 		=> $show_complete_tasks,
	'page404' 					=> $page_404,
]);

$data = [
	'content'   =>  $guest,
	'title'     => 'Дела в порядке',
	'user_name' => 'Константин'
];

if ($_SESSION) {
	$data = ['content' =>  $page_content];
}

// Шаблон страницы
$layout_content = include_template('layout.php', $data);

print($layout_content);
