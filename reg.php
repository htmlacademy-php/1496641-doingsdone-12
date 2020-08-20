<?php

require_once('functions.php');
require_once('data.php');

// Если пользователь зарегистрирован то редирект на главную
if ($user_data) {
    header("Location: index.php");
    exit();
}

/**
 *
 * *ВАЛИДАЦИЯ ФОРМЫ РЕГИСТРАЦИИ
 */

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $form = $_POST;
    $errors = [];
    $warning = 'Пожалуйста, исправьте ошибки в форме';

    // Обязательные поля для заполнения
    $req_fields = ['email', 'password', 'name'];

    foreach ($req_fields as $field) {
        if (empty($form[$field])) {
            $errors[$field] = "Не заполнено поле " . $field;
        }
    }

    // Валидация email
    if (!empty($form['email']) && !filter_var($form['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Некорректный email адрес';
    }

    // Проверим существование email в БД
    if (empty($errors)) {

        // Экранируем спец символы в email от пользователя
        $email = mysqli_real_escape_string($connect, $form['email']);

        // Выборка id пользователя из БД по полю email полученного от пользователя
        $sql_reg = "SELECT user_id FROM user_reg WHERE email = ?";

        // Данные для запроса
        $data = ['email' =>  $email,];

        // Создаем подготовленное выражение
        $stmt_valid_email = db_get_prepare_stmt($connect, $sql_reg, $data);

        // Результат подготовленного запроса в массив
        $result_valid_email = resPreparedQuerySQL($stmt_valid_email);

        // Если id > 0 значит email существует
        if (((int) $result_valid_email) > 0) {
            $errors['email'] = 'Email уже зарегистрирован';
        } else {

            // Добавим нового пользователя в БД
            $password = password_hash($form['password'], PASSWORD_DEFAULT);

            // Запрос на добавление данных в БД
            $sql_add_user = 'INSERT INTO user_reg (date_reg, email, us_name, pass) VALUES (NOW(), ?, ?, ?)';

            // Данные для подготовленного запроса
            $data = [
                'email'     => $form['email'],
                'us_name'   => $form['name'],
                'pass'      => $password,
            ];

            // Создает подготовленное выражение на основе готового SQL запроса и переданных данных
            $stmt_add_user = db_get_prepare_stmt($connect, $sql_add_user, $data);

            // Выполнение подготовленного запроса
            $result_add_user = mysqli_stmt_execute($stmt_add_user);

            // Запишем последний добавленный id пользователя в переменную
            $user_last_id = mysqli_insert_id($connect);

            // Добавим проект "Входящие" для нового пользователя
            $sql_add_proj = mysqli_query($connect, "INSERT INTO project (user_id, proj_name) VALUES ('$user_last_id', 'Входящие')");

            // Выберем все данные нового пользователя
            $sql = "SELECT * FROM user_reg WHERE user_id = '$user_last_id'";

            // Результат в виде массива
            $user = resQueryUser($sql, $connect);

            // Закрываем запрос
            mysqli_stmt_close($stmt_add_user);

            // Закрываем подключение
            mysqli_close($connect);

            // Запишем в сессию данные о пользователе
            $user_data = $user;
        }

        // Редирект на главную если пользователь успешно добавлен в БД
        if ($result_add_user && empty($errors)) {
            $_SESSION['user'] = $user_data;
            header("Location: index.php");
            exit();
        }
    }
}

/**
 *
 * * СОБИРАЕМ ШАБЛОН - РЕГИСТРАЦИЯ НА САЙТЕ
 */

// Данные для передачи в шаблон
$reg_data = [
    'errors'     => $errors,
    'warning'    => $warning,
    'req_fields' => $req_fields,
    'form'       => $form,
];

// Данные для передачи в шаблон
$content_reg = include_template('reg.php', $reg_data);

// Подключаем sidebar для страниц регистрации
$sidebar = ' container--with-sidebar';

// Шаблон страницы регистрации на сайте
$layout_guest = include_template('layout-guest.php', [
    'content'   =>  $content_reg,
    'title'     => 'Document',
    'sidebar'   => $sidebar,
]);

print($layout_guest);
