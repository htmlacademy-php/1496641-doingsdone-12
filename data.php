<?php

// показывать или нет выполненные задачи
$show_complete_tasks = rand(0, 1);

// Устанавливаем time зону по умолчанию
date_default_timezone_set("Europe/Moscow");

// Данные для подключения к БД
$db = [
	'host' 		=> 'localhost',
	'user' 		=> 'root',
	'password' 	=> '',
	'database' 	=> 'doingsdone',
];

// Соединимсяс БД
$connect = mysqli_connect($db['host'], $db['user'], $db['password'], $db['database']);

// Установим кодировку для обмена данными пользователь -> БД
mysqli_set_charset($connect, "utf8");

// Проверка соединения с БД
if (!$db) {
	print('Ошибка подключения к БД: ' . mysqli_connect_error());
};

// Выборка всех проектов из БД
$sql_proj = 'SELECT `user_id`, `proj_id`, `proj_name` 
					FROM project';

// Получаем результат запроса всех проектов в виде массива
$categories = resQuerySQL($sql_proj, $project, $connect);

// получаем количество задач в каждом проекте (где есть задачи)
$count = 'SELECT COUNT(`task_id`) AS count_task, `proj_name` 
				FROM user_reg u, project p, task t 
					WHERE p.proj_id = t.proj_id 
						AND u.user_id = t.user_id 
							GROUP BY `proj_name`';

$count_task = resQuerySQL($count, $task, $connect);

// условие для выборки задач из БД по значению $_GET['id']
if (!empty($_GET['id'])) {
	$proj_id = $_GET['id'];
	settype($proj_id, 'integer'); // устонавливаем тип integer для $_GET
} else {
	$proj_id = 'p.proj_id';
};

// Выборка задач из БД только активного проекта по значению $_GET['id']
$sql_task = "SELECT `proj_name`, `status_task`, `title_task`, `link_file`, `date_task_end` 
					FROM user_reg u, project p, task t 
						WHERE p.proj_id = t.proj_id 
							AND u.user_id = t.user_id
								AND p.proj_id = {$proj_id}";

// Получим результат запроса задач из БД для одного проекта в виде массива
$task_list = resQuerySQL($sql_task, $task, $connect);

// Выборка всех id из БД
$sql_tasks_id = "SELECT `proj_name` FROM project p, user_reg u, task t WHERE p.proj_id = t.proj_id 
AND u.user_id = t.user_id AND t.user_id = p.user_id ";

// Получим результат запроса всех id задач из БД в виде массива
$tasks_id = resQuerySQL($sql_tasks_id, $task, $connect);

// Валидация id
function valTaskID($tasks_id)
{
	foreach ($tasks_id as $key => $value) {
		if ($_GET['id'] == $value['task_id']) {
			return true;
		}
	}
	return false;
}
