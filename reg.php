<?php

require_once('functions.php');
require_once('data.php');

// TODO ВАЛИДАЦИЯ ФОРМЫ РЕГИСТРАЦИИ

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
	if(!empty($form['email']) && !filter_var($form['email'], FILTER_VALIDATE_EMAIL)) {
		$errors['email'] = 'Некорректный email адрес';
	}

	// Проверим существование email в БД
	if (empty($errors)) {

		// Экранируем спец символы в email от пользователя
		$email = mysqli_real_escape_string($connect, $form['email']);

		// Выборка id пользователя из БД по полю email полученного от пользователя
		$sql = "SELECT user_id FROM user_reg WHERE email = '$email'";

		// Результат в виде массива
		$res = resQuerySQL($sql, $connect);

		// Если id > 0 значит email существует
		if (((int)$res) > 0) {

			$errors['email'] = 'Email уже зарегистрирован';

		} else { 

		// Добавим нового пользователя в БД
			$password = password_hash($form['password'], PASSWORD_DEFAULT);

		// Запрос на добавление данных в БД
			$sql = 'INSERT INTO user_reg (date_reg, email, us_name, pass) VALUES (NOW(), ?, ?, ?)';

			$data = [
				'email' 		=> $form['email'],
				'us_name' 	=> $form['name'],
				'pass' 		=> $password,
			];

			$stmt = db_get_prepare_stmt($connect, $sql, $data);

			$res = mysqli_stmt_execute($stmt);
		}

		// Редирект на главную если пользователь успешно добавлен в БД
		if ($res && empty($errors)) {
			header("Location: /index.php");
			exit();
		}

	}
}

// TODO СОБИРАЕМ ШАБЛОН - РЕГИСТРАЦИЯ НА САЙТЕ

// Данные для передачи в шаблон
$tpl_data = [
	'errors' => $errors,
	'warning' => $warning,
	'req_fields' => $req_fields,
	'form' => $form,
];

// Контентная часть
$content_reg = include_template('reg.php', $tpl_data);

// Шаблон страницы
$layout_reg = include_template('layout-reg.php', [
	'content'   =>  $content_reg,
	'title'     => 'Document',
]);

print($layout_reg);
