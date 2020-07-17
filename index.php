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
