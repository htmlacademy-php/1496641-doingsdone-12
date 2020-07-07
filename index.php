<?php

require_once('functions.php');
require_once('data.php');

// Ошибка 404
$page_404 = include_template('404.php', []);

// Контентн для гостя
$guest = include_template('guest.php', []);

// Контентн для авторизированного пользователя
$user_content = include_template('main.php', [
	'projects'					=> $projects,
	'tasks_list'				=> $tasks_list,
	'count_tasks'				=> $count_tasks,
	'projects_and_count_tasks'	=> $projects_and_count_tasks,
	'valid_id'					=> $valid_id,
	'show_complete_tasks' 		=> $show_complete_tasks,
	'page404' 					=> $page_404,
]);

// Проверим авторизацию на сайте (наличие данных в сессии)
if ($us_data['user_id']) {
	$content = $user_content;
} else {
	$content = $guest;
}

// Шаблон главной страницы
$layout_content = include_template('layout.php', [
	'content'   =>  $content,
	'title'     => 'Дела в порядке',
	'us_data' 	=> $us_data,
]);

print($layout_content);
