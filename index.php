<?php

require_once('functions.php');
require_once('data.php');

// Ошибка 404
$page_404 = include_template('404.php', []);

// Контентн для гостя
$guest = include_template('guest.php', []);

// Контентн для авторизированного пользователя
$user = include_template('main.php', [
    'projects'                    => $projects,
    'tasks_list'                => $tasks_list,
    'count_tasks'                => $count_tasks,
    'valid_id'                    => $valid_id,
    'show_complete_tasks'         => $show_complete_tasks,
    'page404'                     => $page_404,
    // 'us_data' 	=> $us_data, // Данные о пользователе в сессии
]);

// Проверим гость или авторизованный пользователь
if ($us_data['user_id']) {
    $layout_template = 'layout.php';
    $content = $user;
} else {
    $layout_template = 'layout-guest.php';
    $content = $guest;
}

// Для страницы главная шаблон гость
$home = 'class="body-background"';

// Шаблон главной страницы
$layout = include_template($layout_template, [
    'content'   =>  $content, // Контент зависит от регистрации
    'title'     => 'Дела в порядке',
    'us_data'     => $us_data, // Данные о пользователе в сессии
    'home'         => $home,
]);

print($layout);
