<?php

session_start();
// error_reporting(E_ALL);

// Передадим все данные о пользователе из сессии в массив $user_data
$user_data = [];

if (isset($_SESSION['user'])) {
    $user_data = $_SESSION['user'];
}


/**
 *
 * * ПОДКЛЮЧЕНИЕ К БД MySQLi
 */

// Устанавливаем time зону по умолчанию
date_default_timezone_set("Europe/Moscow");

// Данные для подключения к БД
$db = [
    'host'      => 'localhost',
    'user'      => 'root',
    'password'  => 'root',
    'database'  => 'doingsdone',
];

// Соединимся БД
$connect = @mysqli_connect($db['host'], $db['user'], $db['password'], $db['database']);

if (!$connect) {
    echo '<strong>Ошибка:</strong> Невозможно установить соединение с MySQL<br>' . PHP_EOL;
    echo '<strong>Код ошибки errno:</strong> ' . mysqli_connect_errno() . '<br>' . PHP_EOL;
    echo '<strong>Текст ошибки error:</strong> ' . mysqli_connect_error() . '<br>' . PHP_EOL;
    // exit;
} else {
    // Установим кодировку для обмена данными пользователь -> БД
    mysqli_set_charset($connect, "utf8");
}

// Получим id пользователя из данных сессии
$user_id = '';

if (isset($_SESSION['user'])) {
    $user_id = $user_data['user_id'];
}

/**
 *
 * * ВЫБОРКА ПРОЕКТОВ И СЧЕТЧИК ЗАДАЧ
 */

$sql_projects = "SELECT p.proj_id, p.proj_name, t.status_task, COUNT(t.task_id) as count
                FROM project p LEFT JOIN task t ON p.proj_id = t.proj_id
                AND t.status_task = 0 WHERE p.user_id ='$user_id'
                GROUP BY p.proj_id";

// Выборка из БД в виде массива
$projects = resQuerySQL($sql_projects, $connect);

/**
 *
 * * ВЫВОД ЗАДАЧ
 */

// Все задачи (актуальные и выполненные)
$status_completed_tasks = 't.status_task';

// Условие для актуальных задач
if (!isset($_GET['show_completed']) || $_GET['show_completed'] == 0) {
    // Только актуальные задачи
    $status_completed_tasks = 0;
}

// Все проекты для выборки
$proj_id = 'p.proj_id';

if (!empty($_GET['id'])) {
    $proj_id = $_GET['id'];
}

// Выборка всех задач для активного пользователя
$sql_tasks = "SELECT p.proj_name, t.task_id, t.status_task, t.title_task, t.link_file,
            DATE_FORMAT(date_task_end, '%Y-%m-%d') AS date_task_end
            FROM project p LEFT JOIN task t ON p.proj_id = t.proj_id
            JOIN user_reg u ON u.user_id = t.user_id
            WHERE u.user_id = $user_id
            AND p.proj_id = $proj_id
            AND t.status_task = $status_completed_tasks
            ORDER BY t.task_id";

// Результат запроса в виде массива
$tasks_list = resQuerySQL($sql_tasks, $connect);

/**
 *
 * * ФОРМА ПОИСКА
 */

// Удалим пробелы из запроса от пользователя
$search = trim($_GET['q'] ?? '');

// Объявим массив для результата поиска
$result_search = [];

// Результат поиска
if ($search) {

    $sql_q = "SELECT t.user_id, t.status_task, t.title_task, t.link_file, t.date_task_end FROM task t
            JOIN user_reg u ON t.user_id = u.user_id
            WHERE MATCH(title_task) AGAINST(? IN BOOLEAN MODE)
            AND u.user_id = {$user_data['user_id']} AND t.status_task = 0";

    // Данные для запроса
    $data_search = ['search' => $search . '*'];

    // Создаем подготовленное выражение
    $stmt = db_get_prepare_stmt($connect, $sql_q, $data_search);

    // Выполнение подготовленного запроса
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);

    // Количество рядов выборки из БД по запросу пользователя
    $num_rows = mysqli_num_rows($result);

    if ($num_rows) {
        // Результат поиска в массив если есть совпадение в БД
        $result_search = mysqli_fetch_all($result, MYSQLI_ASSOC);
    }
}

/**
 *
 * * ПАГИНАЦИЯ
 */

// Условие подсчета количества задач
if (isset($_GET['id'])) {
    // Определим общее количество задач активного проекта
    $sql_cnt_tasks = "SELECT COUNT(*) as cnt FROM task t
                    RIGHT JOIN project p ON p.proj_id = t.proj_id
                    JOIN user_reg u ON u.user_id = t.user_id
                    WHERE p.proj_id = {$_GET['id']}
                    AND u.user_id = $user_id
                    AND t.status_task = $status_completed_tasks";
} else {
    // Определим общее количество задач всех проектов
    $sql_cnt_tasks = "SELECT COUNT(*) as cnt FROM task t
                    JOIN user_reg u ON u.user_id = t.user_id
                    WHERE u.user_id = $user_id
                    AND t.status_task = $status_completed_tasks";
}

if ($connect) {
    $result_all_tasks = mysqli_query($connect, $sql_cnt_tasks);
}

if (!empty($result_all_tasks)) {
    // Количество всех задач пользователя в зависимости от статуса задачи
    $all_tasks = mysqli_fetch_assoc($result_all_tasks)['cnt'];
}
