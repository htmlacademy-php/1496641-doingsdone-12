<?php

require_once('functions.php');
require_once('data.php');

// TODO ВАЛИДАЦИЯ ФОРМЫ ЗАДАЧА

// Определим массив ошибок
$errors = [];

// Проверка, что отправлена форма
if (isset($_POST['submit'])) {

    // Проверяем поля формы на обязательные и незаполненные 
    if (empty($_POST['name'])) {
        $errors['name'] = 'Обязательно для заполнения!';
    }

    // Запишем все id проектов из БД в массив
    foreach ($projects as $project) {
        $projId[] = $project['proj_id'];
    }

    // Проверим id проекта от пользователя с данными в БД
    if (!in_array($_POST['project'], $projId)) {
        $errors['project'] = 'Опа! У нас хакер :-) нет такого проекта';
    }

    // Проверяем дату задачи с учетом текущей даты
    if (!empty($_POST['date']) && ($_POST['date'] < date('Y-m-d'))) {
        $errors['date'] = 'Упсс, дата уже прошла :-(';
    }

    // Проверяем формат даты задачи
    if (!empty($_POST['date']) && is_date_valid($_POST['date']) === false) {
        $errors['date'] = 'Ну ты и хакер :-) формат даты гггг-мм-дд';
    }

    // Работа с файлами
    if (isset($_FILES['file'])) {

        // Присваиваем значения переменным
        $fileName = trim($_FILES['file']['name']);
        $fileSize = $_FILES['file']['size'];
        $fileTmp = $_FILES['file']['tmp_name'];
        $fileErr = $_FILES['file']['error'];

        // Зададим диапазон значений для генерации нового имени файла
        $permitted_chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

        // Определим допустимые типы файлов
        $fileTypes = array('png', 'xlsx', 'xls', 'doc', 'docx', 'pdf', 'jpg', 'csv', 'txt');

        // Получим расширение загруженного файла от пользователя
        $fileExt = strtolower(end(explode('.', $fileName)));

        // Проверим файл на допустимые типы расширений
        if (($fileSize > 0) && in_array($fileExt, $fileTypes)) {

            // Директория для загрузки файла
            $dir = 'uploads/';

            // Формируем рандомно имя файла
            $newFileName = substr(str_shuffle($permitted_chars), 0, 10);

            // Формируем имя файла + расширение
            $newFileName .= '.' . $fileExt;

            // Определим протокол соединения
            $httpHttps = !empty($_SERVER['HTTPS']) ? "https://" : "http://";

            // Формируем ссылку на файл (протокол + домен + директория + файл.расширение)
            $linkFile = $httpHttps . $_SERVER['SERVER_NAME'] . ':8080/' . $dir . $newFileName;
        } elseif ($fileSize > 0) {
            $errors['file'] = 'Фокус не пройдет :-) файл не разрешен';
        }

        // Ошибки сервера которые могут быть
        $fileUploadErrors = [
            // 0 => 'Ошибок не возникло, файл был успешно загружен на сервер',
            1 => 'Размер файла превысил 2Мб',
            // 2 => 'Размер файла превысил значение MAX_FILE_SIZE, указанное в HTML-форме',
            3 => 'Загружаемый файл был получен только частично',
            // 4 => 'Файл не был загружен',
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

// TODO РАБОТА С БД (подготавливаем и выполняем запрос)

if (!empty($_POST) && empty($errors)) {

    // Перемещение файла в директорию uploads если нет ошибок
    move_uploaded_file($fileTmp, $dir . $newFileName);

    // Сформируем подготовленный SQL запрос на добавление новой задачи
    $sql_add_task = "INSERT INTO task(proj_id, user_id, title_task, link_file, date_task_end) 
    VALUES (?, ?, ?, ?, ?)";

    $stmt = mysqli_prepare($connect,  $sql_add_task);

    // Передача значений в подготовленный запрос
    mysqli_stmt_bind_param($stmt, "iisss", $proj_id, $user_id, $title_task, $link_file, $date_task_end);

    $proj_id = $_POST['project'];
    $user_id = $user_id;
    $title_task = $_POST['name'];
    $link_file = (!empty($_FILES['file'])) ? $linkFile : NULL;
    $date_task_end = (!empty($_POST['date'])) ? $_POST['date'] : NULL;

    // Выполнение подготовленного запроса 
    mysqli_stmt_execute($stmt);

    // Закрываем запрос
    mysqli_stmt_close($stmt);

    // Закрываем подключение
    mysqli_close($connect);

    // Редирект пользователя на главную
    sleep(1); // пауза 1 сек
    header('location: index.php');
}

// TODO СОБИРАЕМ ШАБЛОН ДОБАВЛЕНИЕ ЗАДАЧИ

// Контентная часть
$page_content = include_template('add.php', [
    'projects'      => $projects,
    'count_tasks'   => $count_tasks,
    'errors' => $errors,
]);

// Шаблон страницы
$layout_content = include_template('layout.php', [
    'content'   =>  $page_content,
    'title'     => 'Дела в порядке',
    'user_name' => 'Константин'
]);

print($layout_content);
