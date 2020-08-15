<!doctype html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
</head>

<body>
    <p><strong>Задачи на сегодня</strong></p>

    <p>Уважаемый(ая), <?= $us_name; ?></p>
    <p><strong>У вас запланирована(ы) задача(и) на сегодня:</strong></p>
    <ol>
        <?php foreach ($tasks_today as $task) : ?>
            <li><?= $task['title_task']; ?> на <?= $task['date_task_end']; ?></li>
        <?php endforeach; ?>
    </ol>

</body>

</html>
