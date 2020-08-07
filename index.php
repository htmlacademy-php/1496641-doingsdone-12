<?php

require_once 'functions.php';
require_once 'data.php';

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

/**
 *
 * * CHECK - ЗАДАЧА ВЫПОЛНЕНА
 */

// Объявим массив для task_id из БД
$task_id = [];

if (!empty($tasks_list)) {
    // Переберем выборку из БД (многомерный массив) в одномерный нумерованный массив
    foreach ($tasks_list as $key) {
        $task_id[] = $key['task_id'];
    }
}

// id задачи и статус задачи
$get_task_id = (int)$_GET['id_task']; // id задачи
$get_task_complate = (int)$_GET['task_complate']; // статус задачи 0 или 1

// Проверим id задачи от пользователя с БД
$check_id_task = in_array($get_task_id, $task_id);

// Если проверка прошла то делаем запрос к БД на изменение статуса задачи
if ($_SERVER['REQUEST_METHOD'] == 'GET') {

    if ($check_id_task) {

        $sql_task_true = "UPDATE task SET status_task = ? WHERE user_id = ? AND task_id = ?";

        // Данные для запроса
        $data = [
            'status_task' => $get_task_complate,
            'user_id'     => $user_id,
            'task_id'     => $get_task_id,
        ];

        // Создаем подготовленное выражение
        $stmt = db_get_prepare_stmt($connect, $sql_task_true, $data);

        // Выполнение подготовленного запроса
        $res = mysqli_stmt_execute($stmt);

        // После выполнения подготовленного запроса редирект
        if ($res) {
            header("Location: index.php");
        }
    }

    // Значение по умолчанию для задачи
    $show_complete_tasks = 0;

    // Показываем выполненные задачи
    if ($_GET['show_completed']) {
        $show_complete_tasks = 1;
    }
}

/**
 *
 * * ФИЛЬТРЫ ДЛЯ ЗАДАЧ В ПРОЕКТЕ
 */

// Вывод фильтра для задачи "Повестка дня"
if ($_GET['today']) {

    // Текущая дата
    $today = date("Y-m-d");

    // Формируем массив задач на сегодня
    $tasks_list = tasksFilter($tasks_list, $today);
}

// Вывод фильтра для задачи "Завтра"
if ($_GET['tomorrow']) {

    // Получим завтрашний день
    $tomorrow = date("Y-m-d", strtotime("+1 days"));

    // Формируем массив задач на завтра
    $tasks_list = tasksFilter($tasks_list, $tomorrow);
}

// Вывод фильтра для задач "Просроченные"
if ($_GET['old']) {
    $tasks_list = oldTasksFilter($tasks_list);
}
debug($tasks_list);
// Класс для активного фильтра "Все задачи"
$url_domen = $_SERVER['REQUEST_URI'] == "/";

/**
 *
 * * ФОРМИРУЕМ ШАБЛОН
 */

// Данные для передачи в шаблон (для авторизированного пользователя)
$data_user = [
    'projects'              => $projects,
    'tasks_list'            => $tasks_list,
    'count_tasks'           => $count_tasks,
    'valid_id'              => $valid_id,
    'show_complete_tasks'   => $show_complete_tasks,
    'page404'               => $page_404,
    'search'                => $search,
    'res_search'            => $res_search,
    'not_found'             => $not_found,
    'get_task_complate'     => $get_task_complate,
    'get_task_id'           => $get_task_id,
    'check_id_task'         => $check_id_task,
    'class_active'          => $class_active,
    'url_domen'             => $url_domen,
    'pages_count'           => $pages_count,
    'pages'                 => $pages,
    'cur_page'              => $cur_page,
    'pages_prev'            => $pages_prev,
    'pages_next'            => $pages_next,
    'count_task'            => $count_task,
    'filters'               => $filters,
    'filter'                => $filter,
    'items_count'           => $items_count,
    'tasks_items'           => $tasks_items,
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
$layout_data = [
    'content'   => $content, // Контент зависит от регистрации
    'title'     => 'Дела в порядке',
    'us_data'   => $us_data, // Данные о пользователе в сессии
    'home'      => $home,
];

// Шаблон главной страницы
$layout = include_template($layout_tmp, $layout_data);

print($layout);
