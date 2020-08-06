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
    die;
};

/**
 *
 * * ВЫБОРКА ПРОЕКТОВ
 */

// Получим id пользователя из данных сессии
$user_id = $us_data['user_id'];

// Выборка всех проектов из БД
$sql_projects = "SELECT p.proj_id, p.proj_name FROM project p JOIN user_reg u ON p.user_id = u.user_id AND p.user_id ='$user_id'";

// Результат запроса в массив
$projects = resQuerySQL($sql_projects, $connect);

/**
 *
 * * ПАГИНАЦИЯ
 */

// Определим текущую страницу
$cur_page = $_GET['page'] ?? 1;

// Количество задач на странице
$tasks_items = 3;

// Определим обшее количество задач для текущего пользователя
$sql_cnt_tasks = "SELECT COUNT(*) as cnt FROM task t JOIN user_reg u ON u.user_id = t.user_id  WHERE u.user_id = $user_id";

$result = mysqli_query($connect, $sql_cnt_tasks);

// Все задачи пользователя
$items_count = mysqli_fetch_assoc($result)['cnt'];

// Сколько всего страниц (6 задач на страницу)
$pages_count = ceil($items_count / $tasks_items);

// Смещение в зависимости от текущей страницы
$offset = ($cur_page - 1) * $tasks_items;

// Заполним массив номерами всех страниц
$pages = range(1, $pages_count);


debug($items_count);

/**
 *
 * * ВЫВОД ЗАДАЧ СООТВЕТСВУЮЩИХ СВОЕМУ ПРОЕКТУ
 */

// Выборка задач из БД по значению $_GET['id']
if (!empty($_GET['id'])) {
    $proj_id = $_GET['id'];
    settype($proj_id, 'integer'); // Устонавливаем тип integer для $_GET
} else {
    $proj_id = 'p.proj_id';
};

// Выборка всех задач для одного пользователя

// $sql_task = "SELECT proj_name, task_id, status_task, title_task, link_file,
//             DATE_FORMAT(date_task_end, '%Y-%m-%d') AS date_task_end
//             FROM user_reg u, project p, task t WHERE p.proj_id = t.proj_id
//             AND u.user_id = t.user_id AND p.proj_id = $proj_id AND u.user_id = $user_id";

$sql_task = "SELECT p.proj_name, t.task_id, t.status_task, t.title_task, t.link_file,
            DATE_FORMAT(date_task_end, '%Y-%m-%d') AS date_task_end
            FROM project p LEFT JOIN task t ON p.proj_id = t.proj_id
            JOIN user_reg u ON u.user_id = t.user_id
            WHERE u.user_id = $user_id AND p.proj_id = $proj_id
            ORDER BY t.task_id DESC LIMIT $tasks_items OFFSET $offset";

// Результат запроса в виде массива
$tasks_list = resQuerySQL($sql_task, $connect);

// Сортируем задачи в обратном порядке
// if ($tasks_list) {
//     $tasks_list = array_reverse($tasks_list);
// }

/**
 *
 * * СЧЕТЧИК ЗАДАЧ
 */

$sql_cnt_proj = "SELECT p.proj_id, t.status_task, COUNT(t.task_id) as count
                FROM project p LEFT JOIN task t ON p.proj_id = t.proj_id
                AND t.status_task = 0 WHERE p.user_id ='$user_id' GROUP BY p.proj_id";

// Получаем ресурс результата
$result = mysqli_query($connect, $sql_cnt_proj);

// Выборка из БД в виде массива
$count_task = resQuerySQL($sql_cnt_proj, $connect);

// Добавим в массив проектов поле count
foreach ($projects as $key => $value) {

    foreach ($count_task as $k => $v) {

        if ($value['proj_id'] == $v['proj_id']) {

            $value['count'] = $v['count'];
        }
        // Запишем значение count для каждого ключа
        $projects[$key] = $value;
    }
}

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
