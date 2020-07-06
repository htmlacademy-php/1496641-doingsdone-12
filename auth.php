<?php

require_once('functions.php');
require_once('data.php');

// TODO ВАЛИДАЦИЯ ФОРМЫ АВТОРИЗАЦИИ

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $form = $_POST;
    $required = ['email', 'password'];
    $errors = [];

    // Проверим поля на пустоту
    foreach ($required as $field) {
        if (empty($form[$field])) {
            $errors[$field] = 'Это поле надо заполнить';
        }
    }

    // Валидация email
    if (!empty($form['email']) && !filter_var($form['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Некорректный email адрес';
    } else {
        //Экранируем спец символы в email от пользователя
        $email = mysqli_real_escape_string($connect, $form['email']);

        //Найдем в таблице user_reg пользователя с переданным email
        $sql = "SELECT * FROM user_reg WHERE email = '$email'";

        // Результат в виде массива
        $user = resQuerySQL($sql, $connect);

        // Извлекаем из массива пароль
        foreach ($user as $key => $value) {
            $us_pass = $value['pass'];
        }
    }

    // Валидация поля password
    if (!count($errors) && !empty($form['password'])) {

        // Верефикация пароля
        $pass = password_verify($form['password'], $us_pass);

        // Обьявим пустой массив данных о пользователе для передачи в сессию
        $us_data = [];

        // Проверим хэш пароля и откроемм сессию если совпадение
        if ($pass) {

            // Соберем одномерный (из двумерного) ассоциативный массив с данными о пользователе
            foreach ($user as $key => $value) {
                foreach ($value as $key => $value) {
                    $us_data[$key] = $value;
                }
            }

            // Передадим все данные о пользователе в сессию
            $_SESSION['user'] = $us_data;
            header("Location: /index.php");
            exit();
        } else {
            $errors['password'] = 'Неверный пароль';
        }
    }
} else {

    // Если форма не была отправлена проверяем существование сессии
    if (isset($_SESSION['user'])) {
        header("Location: /index.php");
        exit();
    }
}

// TODO СОБИРАЕМ ШАБЛОН - АВТОРИЗАЦИЯ НА САЙТЕ

// Данные для передачи в шаблон
$auth_data = [
    'form'      => $form,
    'errors'    => $errors,
];

// Контентная часть
$content_reg = include_template('auth.php', $auth_data);

// Шаблон страницы
$layout_reg = include_template('layout-reg.php', [
    'content'   =>  $content_reg,
    'title'     => 'Document',
]);

print($layout_reg);
