<?php

require_once('functions.php');
require_once('data.php');

// Ошибка 404
$page_404 = include_template('404.php', []);

// Контентн для гостя
$guest = include_template('guest.php', []);

// Объявим переменную для формы поиска по умолчанию
$not_found = '';

// Если нет совпадений в форме поиска, выводим сообщение
if ($search && !$num_rows) {
    $not_found = '<h4>Ничего не найдено по вашему запросу</h4>';
}

// TODO ЗАДАЧА ВЫПОЛНЕНА

// Объявим массив для task_id из БД
$task_id = [];

// Переберем выборку из БД (многомерный массив) в одномерный нумерованный массив
foreach ($tasks_list as $key) {
    $task_id[] = $key['task_id'];
}

// Данные от пользователя: id задачи и задача выполнена (статус = 1)
$get_task_id = $_GET['id_task']; // id задачи

$get_task_complate = $_GET['task_complate']; // Задача выполнена = 1

// Проверим id задачи от пользователя с БД
$check_id_task = in_array($get_task_id, $task_id);

// Если проверка прошла то запрос к БД
if ($_SERVER['REQUEST_METHOD'] == 'GET') {

    if ($check_id_task) {

        $sql_task_true = "UPDATE task SET status_task = ? WHERE user_id = ? AND task_id = ?";

        // Данные для запроса
        $data = [
            'status_task'   =>  $get_task_complate,
            'user_id'       =>  $user_id,
            'task_id'       =>  $get_task_id,
        ];

        // Создаем подготовленное выражение
        $stmt = db_get_prepare_stmt($connect, $sql_task_true, $data);

        // Выполнение подготовленного запроса
        mysqli_stmt_execute($stmt);

        echo 'ВЫПОЛНЕНО<br>';
    } else {
        echo 'условие НЕ выполняется';
    }
}
echo '<pre>';
var_dump($get_task_complate);
echo '</pre>';

// TODO ФОРМИРУЕМ ШАБЛОН

// Данные для передачи в шаблон (для авторизированного пользователя)
$data_user = [
    'projects'            => $projects,
    'tasks_list'          => $tasks_list,
    'count_tasks'         => $count_tasks,
    'valid_id'            => $valid_id,
    'show_complete_tasks' => $show_complete_tasks,
    'page404'             => $page_404,
    'search'              => $search,
    'res_search'          => $res_search,
    'not_found'           => $not_found,
    'get_task_complate'     => $get_task_complate,
    'get_task_id'           => $get_task_id,
    'check_id_task'         => $check_id_task,
];

// Контентн для авторизированного пользователя
$user = include_template('main.php', $data_user);

// Проверим гость или авторизованный пользователь
if ($us_data['user_id']) {
    $layout_tmp = 'layout.php';
    $content = $user;
} else {
    $layout_tmp = 'layout-guest.php';
    $content = $guest;
}

// Для страницы главная шаблон гость
$home = 'class="body-background"';

// Данные для передачи в шаблон layout
$layout_data =  [
    'content'   =>  $content, // Контент зависит от регистрации
    'title'     => 'Дела в порядке',
    'us_data'   => $us_data, // Данные о пользователе в сессии
    'home'      => $home,
];

// Шаблон главной страницы
$layout = include_template($layout_tmp, $layout_data);

print($layout);
