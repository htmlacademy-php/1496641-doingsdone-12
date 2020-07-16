<?php

require_once('functions.php');
require_once('data.php');

// TODO ВАЛИДАЦИЯ ФОРМЫ, ДОБАВЛЕНИЕ ПРОЕКТА, ВЫВОД ВСЕХ ПРОЕКТОВ ПОЛЬЗОВАТЕЛЯ

$form = $_POST;
$required = ['project_name',];
$errors = [];

// Получим id пользователя из данных сессии
$user_id = $us_data['user_id'];

// Выборка всех проектов из БД
$sql_proj = "SELECT * FROM project WHERE user_id = $user_id";

// Результат запроса в массив
$projects = resQuerySQL($sql_proj, $connect);

// Если форма была отправлена
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // Проверим поля на пустоту
    foreach ($required as $field) {
        if (empty($form[$field])) {
            $errors[$field] = 'Это поле надо заполнить';
        }
    }

    //Найдем в таблице project проект полученный от пользоватлея
    $sql_proj = "SELECT * FROM project WHERE proj_name = ?";

    // Данные для запроса
    $data = ['proj_name' => $form['project_name']];

    // Создаем подготовленное выражение
    $stmt = db_get_prepare_stmt($connect, $sql_proj, $data);

    // Выполнение подготовленного запроса
    mysqli_stmt_execute($stmt);

    // Получим результат из подготовленного запроса
    $res = mysqli_stmt_get_result($stmt);

    // Получим количество рядов в выборке по полю proj_name
    $cnt_proj = mysqli_num_rows($res);

    // Если проект существует в БД значит ошибка
    if ($cnt_proj) {
        $errors[$field] = 'Такой проект уже зарегестрирован вами';
    }

    // Если нет ошибок то добавим проект в БД
    if (!$errors[$field]) {

        $sql_proj = "INSERT INTO project(user_id, proj_name) VALUES (?, ?)";

        // Данные для запроса
        $data_proj = [
            'user_id' => $us_data['user_id'],
            'proj_name' => $form['project_name'],
        ];

        // Создаем подготовленное выражение
        $stmt = db_get_prepare_stmt($connect, $sql_proj, $data_proj);

        // Выполнение подготовленного запроса
        mysqli_stmt_execute($stmt);
    }
}

// TODO СОБИРАЕМ ШАБЛОН - АВТОРИЗАЦИЯ НА САЙТЕ

// Если пользователь зарегестрирован то показываем контент
if ($us_data['user_id']) {

    // Данные для передачи в шаблон
    $proj_data = [
        'projects'    => $projects,
        'count_tasks' => $count_tasks,
        'form'        => $form,
        'errors'      => $errors,
    ];

    // Контент страницы авторизации на сайте
    $content_proj = include_template('proj.php', $proj_data);

    // Шаблон страницы авторизации на сайте
    $layout = include_template('layout.php', [
        'content'  =>  $content_proj,
        'title'    => 'Document',
        'sidebar'  => $sidebar,
        'us_data'  => $us_data, // Данные о пользователе в сессии
    ]);

    print($layout);
} else {
    // Если пользователь не зарегестрирован то переадресуем его на главную
    header('location: /');
}
