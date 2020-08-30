<?php

// Composer autoload
require_once 'vendor/autoload.php';
require_once 'functions.php';
require_once 'data.php';

// Тема письма
$mail_theme = 'Уведомление от сервиса "Дела в порядке"';

// Данные для доступа к SMTP-серверу
$transport = new Swift_SmtpTransport('smtp.mailtrap.io', 2525);
$transport->setUsername('e3d309a7411847');
$transport->setPassword('b25f5ff5f49670');

$mailer = new Swift_Mailer($transport);

$sql = 'SELECT * FROM user_reg';

$result_users = mysqli_query($connect, $sql);

$users = mysqli_fetch_all($result_users, MYSQLI_ASSOC);

foreach ($users as $key => $value) {

    $sql = "SELECT * FROM task WHERE status_task = 0 AND date_task_end = CURDATE() AND task.user_id = " . $value['user_id'];

    $result_tasks_today = mysqli_query($connect, $sql);

    $tasks_today = mysqli_fetch_all($result_tasks_today, MYSQLI_ASSOC);

    if (!empty($tasks_today)) {

        $message = new Swift_Message();
        $message->setSubject($mail_theme);
        $message->setFrom(['152185c2bf-7986d4@inbox.mailtrap.io' => 'Дела в порядке']);
        $message->setTo([$value['email'] => $value['us_name']]);

        // Данные для передачи в шаблон
        $data_mess = [
            'tasks_today' => $tasks_today,
            'us_name'     => $value['us_name'],
        ];

        // Контент письма
        $mess_content = include_template('mess.php', $data_mess);

        $message->setBody($mess_content, 'text/html');

        // Отправляем подготовленное сообщение и получаем результат
        $result = $mailer->send($message);
    }
}
