<?php

require_once('functions.php');
require_once('data.php');

/**
 *
 * * ВАЛИДАЦИЯ ФОРМЫ ЗАДАЧА
 */

$form = $_POST;

// Определим массив ошибок
$errors = [];

// Определим переменную для выбранного проекта
$project_id = '';

// Проверка, что отправлена форма
if (isset($form['submit'])) {

    // Проверяем поля формы на обязательные и незаполненные
    if (empty($form['name'])) {
        $errors['name'] = 'Обязательно для заполнения!';
    }

    // Запишем все id проектов из БД в массив
    foreach ($projects as $project) {
        $projId[] = $project['proj_id'];
    }

    // Проверим id проекта от пользователя с данными в БД
    if (!in_array($form['project'], $projId)) {
        $errors['project'] = 'Опа! У нас хакер :-) нет такого проекта';
    } else {
        $project_id = $form['project'];
    }

    // Проверяем дату задачи с учетом текущей даты
    if (!empty($form['date']) && ($form['date'] < date('Y-m-d'))) {
        $errors['date'] = 'Упсс, дата уже прошла :-(';
    }

    // Проверяем формат даты задачи
    if (!empty($form['date']) && is_date_valid($form['date']) === false) {
        $errors['date'] = 'Ну ты и хакер :-) формат даты гггг-мм-дд';
    }

    // Работа с файлами
    if (isset($_FILES['file'])) {

        // Присваиваем значения переменным
        $fileName   = trim($_FILES['file']['name']);
        $fileSize   = $_FILES['file']['size'];
        $fileTmp    = $_FILES['file']['tmp_name'];
        $fileErr    = $_FILES['file']['error'];

        // Определим допустимые типы файлов
        $fileTypes = array('png', 'xlsx', 'xls', 'doc', 'docx', 'pdf', 'jpg', 'csv', 'txt');

        // Получим расширение загруженного файла от пользователя
        $fileExt = strtolower(end(explode('.', $fileName)));

        // Проверим файл на допустимые типы расширений
        if (($fileSize > 0) && in_array($fileExt, $fileTypes)) {

            // Директория для загрузки файла
            $dir = 'uploads/';

            // Формируем имя файла
            $newFileName = time() . '_' . $user_id;

            // Формируем имя файла + расширение
            $newFileName .= '.' . $fileExt;

            // Формируем ссылку на файл (директория + файл.расширение)
            $linkFile = $dir . $newFileName;
        } elseif ($fileSize > 0) {
            $errors['file'] = 'Фокус не пройдет :-) файл не разрешен';
        }

        // Ошибки сервера которые могут быть
        $fileUploadErrors = [
            1 => 'Размер файла превысил 2Мб',
            3 => 'Загружаемый файл был получен только частично',
            6 => 'Отсутствует временная папка',
            7 => 'Не удалось записать файл на диск',
            8 => 'Не допустимое расширение файла',
        ];

        // Проверяем загруженный файл на ошибки сервера
        if ($fileName && $fileErr != 0) {
            // Запишем ошибку в массив ошибок
            foreach ($fileUploadErrors as $errs) {
                if ($errs = $fileErr) {
                    $errors['file'] = $fileUploadErrors[$errs];
                }
            }
        }
    }
}

/**
 *
 * * ДОБАВЛЕНИЕ ЗАДАЧИ В ПРОЕКТ
 */

if (!empty($form) && empty($errors)) {

    // Перемещение файла в директорию uploads если нет ошибок
    move_uploaded_file($fileTmp, $dir . $newFileName);

    // $user_id = $_SESSION['user']['user_id'];

    // Сформируем подготовленный SQL запрос на добавление новой задачи
    $sql_add_task = "INSERT INTO task(proj_id, user_id, title_task, link_file, date_task_end)
    VALUES (?, ?, ?, ?, ?)";

    // Данные для запроса
    $data = [
        'proj_id'       => $form['project'],
        'user_id'       => $user_id,
        'title_task'    => $form['name'],
        'link_file'     => (!empty($_FILES['file'])) ? $linkFile : NULL,
        'date_task_end' => (!empty($form['date'])) ? $form['date'] : NULL,
    ];

    // Создаем подготовленное выражение
    $stmt = db_get_prepare_stmt($connect, $sql_add_task, $data);

    // Выполнение подготовленного запроса
    mysqli_stmt_execute($stmt);

    // Закрываем запрос
    mysqli_stmt_close($stmt);

    // Закрываем подключение
    mysqli_close($connect);

    // Редирект пользователя на главную
    header('location: index.php');
}

/**
 *
 * * ФОРМИРУЕМ ШАБЛОН
 */

// Проверим авторизацию на сайте (наличие данных в сессии)
if ($us_data['user_id']) {

    // Данные для шаблона
    $page_content = include_template('add.php', [
        'projects'      => $projects,
        'count_tasks'   => $count_tasks,
        'errors'        => $errors,
        'project_id'    => $project_id,
        'form'          => $form,
    ]);

    // Шаблон страницы
    $layout_content = include_template('layout.php', [
        'content' =>  $page_content,
        'title'   => 'Дела в порядке',
        'us_data' => $us_data,
    ]);

    print($layout_content);
} else {
    // Редирект пользователя на главную для регистрации
    header('location: /');
}
