<?php

require_once('functions.php');
require_once('data.php');

// Если пользователь зарегестрирован то редирект на главную
if ($us_data) {
    header("Location: index.php");
    exit();
}

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

    // Валидация email - из чего должен состоять email
    $email_filter = filter_var($form['email'], FILTER_VALIDATE_EMAIL);

    if (!empty($form['email']) && !$email_filter) {
        $errors['email'] = 'Некорректный email адрес';
    } else {

        //Экранируем спец символы в email от пользователя
        $email = mysqli_real_escape_string($connect, $form['email']);

        //Найдем в таблице user_reg пользователя с переданным email
        $sql = "SELECT * FROM user_reg WHERE email = '$email'";

        // Результат в виде массива
        $user = resQueryUser($sql, $connect);

        // Запишем в сесию данные о пользователе
        $us_data = $user;

        // Валидация поля email - проверка есть или нет в БД
        $cnt_email = sqlNumRows($sql, $connect);
        if (!empty($form['email']) && !$cnt_email) {
            $errors['email'] = 'Такой email не зарегистрирован';
        }
    }

    // Валидация поля password
    if (!count($errors) && !empty($form['password'])) {

        // Запишем пароль в переменную
        $us_pass = $us_data['pass'];

        // Верефикация пароля
        $pass = password_verify($form['password'], $us_pass);

        // Проверим хэш пароля и откроемм сессию если совпадение
        if ($pass) {
            $_SESSION['user'] = $us_data;
            header("Location: index.php");
            exit();
        } else {
            $errors['password'] = 'Неверный пароль';
        }
    } else {

        // Если форма не была отправлена проверим существование сессии
        if (isset($_SESSION['user']['user_id'])) {
            header("Location: index.php");
            exit();
        }
    }
}

// TODO СОБИРАЕМ ШАБЛОН - АВТОРИЗАЦИЯ НА САЙТЕ

// Данные для передачи в шаблон
$auth_data = [
    'form'      => $form,
    'errors'    => $errors,
];

// Контент страницы авторизации на сайте
$content_auth = include_template('auth.php', $auth_data);

// Подключаем sidebar для страниц регестрации
$sidebar = ' container--with-sidebar';

// Шаблон страницы авторизации на сайте
$layout_guest = include_template('layout-guest.php', [
    'content'   =>  $content_auth,
    'title'     => 'Document',
    'sidebar'   => $sidebar,
]);

print($layout_guest);
