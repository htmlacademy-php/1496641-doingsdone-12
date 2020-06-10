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
$sql_proj = 'SELECT `user_id`, `proj_id`, `proj_name` FROM project';

// Получаем результат запроса всех проектов в виде массива
$categories = resQuerySQL($sql_proj, $project, $connect);

// условие для выборки задач из БД
// isset — Определяет, была ли установлена переменная значением, отличным от NULL
// empty -- Проверяет, считается ли переменная пустой. Переменная считается пустой, если она не существует или её значение равно FALSE.
if (!empty($_GET['id'])) {

	$get_proj = $_GET['id']; 

// } elseif (!$_GET['id'])) {

// 	echo 'id не существует';

} else {
	// выборка всех задач
	$get_proj = 'p.proj_id';
	
}




// Выборка задач из БД только указанного проекта по значению $get_proj
$sql_task = 'SELECT `proj_name`, `status_task`, `title_task`, `link_file`, `date_task_end` 
FROM user_reg u, project p, task t 
WHERE p.proj_id = t.proj_id 
AND u.user_id = t.user_id 
AND p.proj_id ='. $get_proj;

// Получим результат запоса всех задач из БД в виде массива
$tasks_list = resQuerySQL($sql_task, $task, $connect);

