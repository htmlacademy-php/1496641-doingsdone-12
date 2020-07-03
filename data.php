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

// Удалить после того как появиться форма регистрации на сайте
$user_id = 1;

// Выборка всех проектов из БД
$sql_proj = "SELECT user_id, proj_id, proj_name
				FROM project WHERE user_id = {$user_id}";

// Результат запроса в виде массива
$projects = resQuerySQL($sql_proj, $connect);

// TODO ФОРМИРУЕМ ДАННЫЕ ДЛЯ СЧЕТЧИКА ЗАДАЧ В ПРОЕКТАХ

// Количество задач в каждом проекте (только для проектов где есть задачи)
$sql_count = "SELECT COUNT(task_id) AS count_task, proj_name 
FROM user_reg u, project p, task t 
WHERE p.proj_id = t.proj_id 
AND u.user_id = t.user_id 
GROUP BY proj_name";

// Результат запроса в виде массива
$count_tasks = resQuerySQL($sql_count, $connect);

// TODO ДЕЛАЕМ ИНТЕРАКТИВ - КЛИКАБЕЛЬНОЕ МЕНЮ ИЗ ПРОЕКТОВ (ПРОЕКТ - ЗАДАЧИ)

// условие для выборки задач из БД по значению $_GET['id'],
if (!empty($_GET['id'])) {
	$proj_id = $_GET['id'];
	settype($proj_id, 'integer'); // устонавливаем тип integer для $_GET
} else {
	$proj_id = 'p.proj_id';
};

// Выборка задач из БД только активного проекта по значению $_GET['id']
$sql_task = "SELECT proj_name, status_task, title_task, link_file, date_task_end 
FROM user_reg u, project p, task t 
WHERE p.proj_id = t.proj_id 
AND u.user_id = t.user_id
AND p.proj_id = {$proj_id}";

// Результат запроса в виде массива
$tasks_list = resQuerySQL($sql_task, $connect);

// Сортируем задачи в обратном порядке
$tasks_list = array_reverse($tasks_list);

// Выборка всех проектов и количество задач в них для всех пользователей
$sql_projects_and_count_tasks = "SELECT p.proj_id, p.proj_name, COUNT(t.task_id) as count 
FROM project p LEFT JOIN task t ON p.proj_id = t.proj_id GROUP BY p.proj_id";

//Выборка всех проектов и количество задач в них для одного пользователя с user_id = 1 
// $sql_projects_and_count_tasks = "SELECT p.proj_id, p.proj_name, COUNT(t.task_id) as count 
// 													FROM project p LEFT JOIN task t ON p.proj_id = t.proj_id 
// 															AND p.user_id =1 AND t.user_id = 1 GROUP BY p.proj_id";

// Результат запроса в виде массива
$projects_and_count_tasks = resQuerySQL($sql_projects_and_count_tasks, $connect);
