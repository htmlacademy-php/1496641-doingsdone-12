<?php

// показывать или нет выполненные задачи
$show_complete_tasks = rand(0, 1);

//  TODO ПОДКЛЮЧЕНИЕ К БАЗЕ ДАННЫХ MySQLi

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

// TODO ФОРМИРУЕМ ДАННЫЕ ДЛЯ ВЫВОДА ПРОЕКТОВ

// Выборка всех проектов из БД
$sql_proj = 'SELECT `user_id`, `proj_id`, `proj_name` 
					FROM project';

// Получаем результат запроса всех проектов в виде массива
$categories = resQuerySQL($sql_proj, $connect);

// TODO ФОРМИРУЕМ ДАННЫЕ ДЛЯ СЧЕТЧИКА ЗАДАЧ В ПРОЕКТАХ

// получаем количество задач в каждом проекте (где есть задачи)
$sql_count = 'SELECT COUNT(`task_id`) AS count_task, `proj_name` 
				FROM user_reg u, project p, task t 
					WHERE p.proj_id = t.proj_id 
						AND u.user_id = t.user_id 
							GROUP BY `proj_name`';

// Результат запроса в виде массива
$count_task = resQuerySQL($sql_count, $connect);

// TODO ДЕЛАЕМ ИНТЕРАКТИВ - КЛИКАБЕЛЬНОЕ МЕНЮ ИЗ ПРОЕКТОВ (ПРОЕКТ - ЗАДАЧИ)

// условие для выборки задач из БД по значению $_GET['id'],
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

// Результат запроса в виде массива
$task_list = resQuerySQL($sql_task, $connect);

// Выборка всех задач из БД если нет GET запросов
$sql_tasks_id = "SELECT `task_id` FROM task";

// Результат запроса в виде массива
$tasks_id = resQuerySQL($sql_tasks_id, $connect);

// TODO ВЫБОРКА ВСЕХ ПРОЕКТОВ У КОТОРЫХ НЕТ ЗАДАЧ

// 1. Работаем с данными таблицы задачи (task)

// Выборка всех id рубрик из таблицы задач
$sql_projname_from_tasks = "SELECT p.`proj_id` FROM project p, task t WHERE p.proj_id = t.proj_id";

// Результат запишем в массив
$projname_from_tasks = resQuerySQL($sql_projname_from_tasks, $connect);

// Переберем двумерный массив в новый одномерный
foreach ($projname_from_tasks as $key => $value) {
	$proj_task[] = $value['proj_id'];
}

// 2. Работаем с данными из таблицы проекты (project)

// Выборка всех id рубрик из таблицы проекты
$sql_projname_project = "SELECT `proj_id` FROM project";

// Результата запишем в массив
$projname_project = resQuerySQL($sql_projname_project, $connect);

// Переберем двумерный массив в новый одномерный
foreach ($projname_project as $key => $value) {
	$proj_project[] = $value['proj_id'];
}

// 3. Сравним два массива $proj_task и $proj_project и получаем id рубрик без задач
$projname_not_task = array_diff($proj_project, $proj_task);

// TODO КОНЕЦ