<?php

session_start();

// Передадим все данные о пользователе из сессии в переменную $us_data
$us_data = $_SESSION['user'];

/**
 * 
 * * ПОДКЛЮЧЕНИЕ К БД MySQLi
 */

// Устанавливаем time зону по умолчанию
date_default_timezone_set("Europe/Moscow");

// Данные для подключения к БД
$db = [
    'host' => 'localhost',
    'user' => 'root',
    'password' => '',
    'database' => 'doingsdone',
];

// Соединимсяс БД
$connect = mysqli_connect($db['host'], $db['user'], $db['password'], $db['database']);

// Установим кодировку для обмена данными пользователь -> БД
mysqli_set_charset($connect, "utf8");

// Проверка соединения с БД
if (!$db) {
    print('Ошибка подключения к БД: ' . mysqli_connect_error());
};

/**
 * 
 * * ВЫБОРКА ПРОЕКТОВ
 */

// Получим id пользователя из данных сессии
$user_id = $us_data['user_id'];

// Выборка всех проектов из БД
$sql_proj = "SELECT proj_id, proj_name FROM project WHERE user_id = $user_id";

// Результат запроса в массив
$projects = resQuerySQL($sql_proj, $connect);

/**
 *  
 * * ВЫВОД ЗАДАЧ СООТВЕТСВУЮЩИХ СВОЕМУ ПРОЕКТУ
 */

// Выборка задач из БД по значению $_GET['id'],
if (!empty($_GET['id'])) {
    $proj_id = $_GET['id'];
    settype($proj_id, 'integer'); // Устонавливаем тип integer для $_GET
} else {
    $proj_id = 'p.proj_id';
};

// Выборка всех задач для одного пользователя
$sql_task = "SELECT proj_name, task_id, status_task, title_task, link_file,
                    DATE_FORMAT(date_task_end, '%Y-%m-%e') AS date_task_end
                        FROM user_reg u, project p, task t
                            WHERE p.proj_id = t.proj_id
                                AND u.user_id = t.user_id
                                    AND p.proj_id = $proj_id
                                        AND u.user_id = $user_id";

// Результат запроса в виде массива
$tasks_list = resQuerySQL($sql_task, $connect);

// Сортируем задачи в обратном порядке
if ($tasks_list) {
    $tasks_list = array_reverse($tasks_list);
}

/**
 * 
 * * ФОРМИРУЕМ ДАННЫЕ ДЛЯ СЧЕТЧИКА ЗАДАЧ В ПРОЕКТАХ
 */

// Выборка всех проектов и количество задач в них
$sql_count_tasks = "SELECT p.proj_id, p.proj_name, COUNT(t.task_id) as count
						FROM project p LEFT JOIN task t ON p.proj_id = t.proj_id
							WHERE p.user_id ='$user_id' AND t.status_task = 0
								GROUP BY p.proj_id";

// Результат запроса в виде массива
$count_tasks = resQuerySQL($sql_count_tasks, $connect);

/**
 * 
 * * ФОРМА ПОИСКА
 */

$search = trim($_GET['q']) ?? '';

// Результат поиска
if ($search) {

    $sql_q = "SELECT * FROM task WHERE (user_id = {$us_data['user_id']})
                AND MATCH (title_task) AGAINST(? IN BOOLEAN MODE)";

    // Данные для запроса
    $data = ['search' => $search . '*'];

    // Создаем подготовленное выражение
    $stmt = db_get_prepare_stmt($connect, $sql_q, $data);

    // Выполнение подготовленного запроса
    mysqli_stmt_execute($stmt);

    $res = mysqli_stmt_get_result($stmt);

    // Количество рядов выборки из БД по запросу пользователя
    $num_rows = mysqli_num_rows($res);

    // Результат поиска в массив если есть совпадение в БД
    $res_search = mysqli_fetch_all($res, MYSQLI_ASSOC);

    // Закрываем запрос
    mysqli_stmt_close($stmt);

    // Закрываем подключение
    mysqli_close($connect);
}
