<?php

require_once 'functions.php';
require_once 'data.php';

// Ошибка 404
$page_404 = include_template('404.php', []);

// Контент для гостя
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

// Значение статуса задачи по умолчанию (не выполнено)
$get_task_completed = (int)$_GET['task_completed'] ?? 0;

// значение выполненных задач по умолчанию
$show_completed_tasks = (int)$_GET['show_completed'] ?? 0;

// Проверяем значение "Показывать выполненные" на false
// и назначаем противоположные для статуса задачи true
if (!$show_completed_tasks) {
    $get_task_completed = 1;
}

// Проверим id задачи от пользователя с БД
$check_id_task = in_array($get_task_id, $task_id);

// Если проверка прошла то делаем запрос к БД на изменение статуса задачи
if ($_SERVER['REQUEST_METHOD'] == 'GET') {

    if ($check_id_task) {

        $sql_task_true = "UPDATE task SET status_task = ? WHERE user_id = ? AND task_id = ?";

        // Данные для запроса
        $data = [
            'status_task' => $get_task_completed,
            'user_id'     => $user_id,
            'task_id'     => $get_task_id,
        ];

        // Создаем подготовленное выражение
        $stmt = db_get_prepare_stmt($connect, $sql_task_true, $data);

        // Выполнение подготовленного запроса
        $result = mysqli_stmt_execute($stmt);

        // После выполнения подготовленного запроса редирект
        if ($result) {
            header("Location: index.php");
        }
    }

    // Значение по умолчанию для "Показывать выполненные"
    $show_completed_tasks = 0;

    // Показываем выполненные задачи
    if ($_GET['show_completed']) {
        $show_completed_tasks = 1;
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

    if ($tasks_list) {
        // Количество задач, результат работы фильтра
        $filter_all_tasks = count($tasks_list);

        // Перепишем общее количество задач согласно фильтра для пагинации
        $all_tasks =  $filter_all_tasks;
    }
}

// Вывод фильтра для задачи "Завтра"
if ($_GET['tomorrow']) {

    // Получим завтрашний день
    $tomorrow = date("Y-m-d", strtotime("+1 days"));

    // Формируем массив задач на завтра
    $tasks_list = tasksFilter($tasks_list, $tomorrow);

    if ($tasks_list) {
        // Количество задач, результат работы фильтра
        $filter_all_tasks = count($tasks_list);

        // Перепишем общее количество задач согласно фильтра для пагинации
        $all_tasks =  $filter_all_tasks;
    }
}

// Вывод фильтра для задач "Просроченные"
if ($_GET['old']) {
    $tasks_list = oldTasksFilter($tasks_list);

    if ($tasks_list) {
        // Количество задач, результат работы фильтра
        $filter_all_tasks = count($tasks_list);

        // Перепишем общее количество задач согласно фильтра для пагинации
        $all_tasks =  $filter_all_tasks;
    }
}

// Класс для активного фильтра "Все задачи"
$url_domain = $_SERVER['REQUEST_URI'] == "/";

// Запишем ключи массива $_GET для фильтров в новый массив
$filters = ['all', 'today', 'tomorrow', 'old'];

// Переберем массив $filters
foreach ($filters as $key) {
    // При совпадении значения массива $filters с ключом массива $_GET
    // Формируем ссылку на активный фильтр
    if ($_GET[$key]) {
        $filter = '&' . $key . '=1';
    }
}

/**
 *
 * * ПАГИНАЦИЯ
 */

// Определим текущую страницу
$cur_page = $_GET['page'] ?? 1;

// Количество задач на одной странице
$task_one_page = 3;

// Общее количество страниц
if ($filter_all_tasks) {
    // Если активен фильтр
    $pages_count = ceil($filter_all_tasks / $task_one_page);
} else {
    // Количество всех задач без учета фильтров
    $pages_count = ceil($all_tasks / $task_one_page);
}

// Заполним массив номерами всех страниц
$pages = range(1, $pages_count);


/**
 *
 * * ФОРМИРУЕМ ШАБЛОН
 */

// Данные для передачи в шаблон (для авторизированного пользователя)
$data_user = [
    'projects'              => $projects,
    'tasks_list'            => $tasks_list,
    // 'count_tasks'           => $count_tasks,
    'valid_id'              => $valid_id,
    'show_completed_tasks'  => $show_completed_tasks,
    'page404'               => $page_404,
    'search'                => $search,
    'res_search'            => $res_search,
    'not_found'             => $not_found,
    'get_task_completed'    => $get_task_completed,
    'get_task_id'           => $get_task_id,
    'check_id_task'         => $check_id_task,
    'class_active'          => $class_active,
    'url_domain'            => $url_domain,
    'pages_count'           => $pages_count,
    'pages'                 => $pages,
    'cur_page'              => $cur_page,
    'pages_prev'            => $pages_prev,
    'pages_next'            => $pages_next,
    // 'count_task'            => $count_task,
    'filters'               => $filters,
    'filter'                => $filter,
    'all_tasks'             => $all_tasks,
    'task_one_page'         => $task_one_page,
    'filter_all_tasks'      => $filter_all_tasks,
];

// Контент для авторизированного пользователя
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
