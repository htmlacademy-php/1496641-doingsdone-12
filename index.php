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

// Значение id проекта по умолчанию
$get_id = getParameter('id', 0);

// Объявим массив значений id проектов для валидации
$valid_id = [];

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

// Значение id задачи от пользователя по умолчанию
$get_task_id = getParameter('id_task', 0);

// Значение статуса задачи по умолчанию (не выполнено)
$get_task_completed = getParameter('task_completed', 0);

// значение выполненных задач по умолчанию
$show_completed_tasks = getParameter('show_completed', 0);

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
    if (getParameter('show_completed', 0)) {
        $show_completed_tasks = 1;
    }
}

/**
 *
 * * ФИЛЬТРЫ ДЛЯ ЗАДАЧ В ПРОЕКТЕ
 */

// Значение количества задач по умолчанию
$all_tasks = 0;

// Объявим переменную для количество задач согласно фильтра
$filter_all_tasks = '';

// Значение фильтра "Все задачи"
$get_all = getParameter('all', 0);

// Вывод фильтра для задачи "Повестка дня"
$get_today = getParameter('today', 0);

if ($get_today) {

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
$get_tomorrow = getParameter('tomorrow', 0);

if ($get_tomorrow) {

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
$get_old = getParameter('old', 0);

if ($get_old) {

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

// Объявим переменную - ссылка на активный фильтр
$active_filter_link = '';

// Формируем url для активного фильтра
foreach ($filters as $filter) {

    /**
     * При совпадении значения массива $filters с ключом массива $_GET
     * формируем ссылку на активный фильтр
     */

    if (isset($_GET[$filter])) {
        $active_filter_link = '&' . $filter . '=1';
    }
}

/**
 *
 * * ПАГИНАЦИЯ
 */

// Определим текущую страницу
$cur_page = getParameter('page', 1);

// Количество задач на одной странице
$task_one_page = 3;

// Общее количество страниц
if ($filter_all_tasks) {
    // Если активен фильтр
    $pages_count = ceil($filter_all_tasks / $task_one_page);
} else {
    if (isset($tasks_list)) {
        $all_tasks = count($tasks_list);
    }
    // Количество всех задач без учета фильтров
    $pages_count = intval(ceil($all_tasks / $task_one_page));
}

// Заполним массив номерами всех страниц
$pages = range(1, intval($pages_count));

// Предыдущая страница
if ($cur_page > 1) {
    $pages_prev = $cur_page - 1;
} else {
    $pages_prev = 1;
}

// Следующая страница
if ($cur_page < $pages_count) {
    $pages_next = $cur_page + 1;
} else {
    $pages_next = $pages_count;
}

/**
 *
 * * ФОРМИРУЕМ ШАБЛОН
 */

// Данные для передачи в шаблон (для авторизированного пользователя)
$data_user = [
    'projects'              => $projects,
    'tasks_list'            => $tasks_list,
    'valid_id'              => $valid_id,
    'show_completed_tasks'  => $show_completed_tasks,
    'page404'               => $page_404,
    'search'                => $search,
    'result_search'         => $result_search,
    'not_found'             => $not_found,
    'get_task_completed'    => $get_task_completed,
    'get_task_id'           => $get_task_id,
    'check_id_task'         => $check_id_task,
    'url_domain'            => $url_domain,
    'pages_count'           => $pages_count,
    'pages'                 => $pages,
    'cur_page'              => $cur_page,
    'pages_prev'            => $pages_prev,
    'pages_next'            => $pages_next,
    'filters'               => $filters,
    'all_tasks'             => $all_tasks,
    'task_one_page'         => $task_one_page,
    'filter_all_tasks'      => $filter_all_tasks,
    'active_filter_link'    => $active_filter_link,
    'get_id'                => $get_id,
    'get_all'               => $get_all,
    'get_today'             => $get_today,
    'get_tomorrow'          => $get_tomorrow,
    'get_old'               => $get_old,

];

// Контент для авторизированного пользователя
$user = include_template('main.php', $data_user);

// Проверим гость или авторизованный пользователь
if (isset($user_data['user_id'])) {
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
    'user_data' => $user_data, // Данные о пользователе в сессии
    'home'      => $home,
];

// Шаблон главной страницы
$layout = include_template($layout_tmp, $layout_data);

print($layout);
