<?php

require_once('functions.php');
require_once('data.php');

/**
 *
 * * ВАЛИДАЦИЯ ФОРМЫ, ДОБАВЛЕНИЕ ПРОЕКТА
 */

$form = $_POST;

// Обязательное поле для заполнения
$required = ['project_name',];

// Массив ошибок
$errors = [];

// Если форма была отправлена
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // Проверим поля на пустоту
    foreach ($required as $field) {
        if (empty($form[$field])) {
            $errors[$field] = 'Это поле надо заполнить';
        }
    }

    //Найдем в таблице project проект полученный от пользователя
    $sql_proj = "SELECT p.proj_id, p.proj_name FROM project p
                JOIN user_reg u ON u.user_id = p.user_id
                WHERE u.user_id = '$user_id' AND p.proj_name = ?";

    // Данные для запроса
    $data = ['proj_name' => $form['project_name']];

    // Создаем подготовленное выражение
    $stmt = db_get_prepare_stmt($connect, $sql_proj, $data);

    // Выполнение подготовленного запроса
    mysqli_stmt_execute($stmt);

    // Получим результат из подготовленного запроса
    $result = mysqli_stmt_get_result($stmt);

    // Получим количество рядов в выборке по полю proj_name
    $cnt_proj = mysqli_num_rows($result);

    // Если проект существует в БД значит ошибка
    if ($cnt_proj) {
        $errors[$field] = 'Такой проект уже зарегистрирован вами';
    }

    // Если нет ошибок то добавим проект в БД
    if (!isset($errors[$field])) {

        $sql_proj = "INSERT INTO project(user_id, proj_name) VALUES (?, ?)";

        // Данные для запроса
        $data_proj = [
            'user_id' => $user_data['user_id'],
            'proj_name' => $form['project_name'],
        ];

        // Создаем подготовленное выражение
        $stmt = db_get_prepare_stmt($connect, $sql_proj, $data_proj);

        // Выполнение подготовленного запроса
        mysqli_stmt_execute($stmt);

        // Добавим новую категорию в конец всех категорий
        $projects[] = ['proj_name' => $form['project_name']];

        // Редирект на главную
        header("Location: index.php");
        exit();
    }
}

/**
 *
 * * СОБИРАЕМ ШАБЛОН - АВТОРИЗАЦИЯ НА САЙТЕ
 */

// Если пользователь зарегистрирован тогда показываем контент
if ($user_data['user_id']) {

    // Данные для передачи в шаблон
    $proj_data = [
        'form'        => $form,
        'errors'      => $errors,
        'projects'    => $projects,
    ];

    // Контент страницы авторизации на сайте
    $content_proj = include_template('proj.php', $proj_data);

    // Подключаем sidebar для страниц регистрации
    $sidebar = ' container--with-sidebar';

    // Шаблон страницы добавление проекта
    $layout_data = [
        'content'    =>  $content_proj,
        'title'      => 'Document',
        'sidebar'    => $sidebar,
        'user_data'  => $user_data, // Данные о пользователе в сессии
    ];

    $layout = include_template('layout.php', $layout_data);

    print($layout);
} else {
    // Если не зарегистрирован тогда редирект на главную
    header('location: index.php');
}
