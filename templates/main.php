<div class="content">
    <section class="content__side">

        <h2 class="content__side-heading">Проекты</h2>

        <nav class="main-navigation">
            <?php foreach ($projects as $key => $value) : ?>
                <ul class="main-navigation__list">
                    <li class="main-navigation__list-item <?= $_GET['id'] == $value['proj_id'] ? 'main-navigation__list-item--active' : '' ?>">
                        <a class="main-navigation__list-item-link" href="<?= 'index.php?id=' . $value['proj_id'] ?>"><?= htmlspecialchars($value['proj_name']) ?></a>
                        <span class="main-navigation__list-item-count"><?= $value['count']; ?></span>
                    </li>
                </ul>
            <?php endforeach; ?>
        </nav>

        <a class="button button--transparent button--plus content__side-button" href="/proj.php" target="project_add">Добавить проект</a>

    </section>

    <main class="content__main">
        <?php if (!$search) : ?>
            <h2 class="content__main-heading">Список задач</h2>
        <?php else : ?>
            <h2 class="content__main-heading">Результат поиска</h2>
        <?php endif; ?>

        <form class="search-form" action="index.php" method="get" autocomplete="off">
            <input class="search-form__input" type="text" name="q" value="" placeholder="Поиск по задачам">

            <input class="search-form__submit" type="submit" name="" value="Искать">
        </form>

        <div class="tasks-controls">
            <nav class="tasks-switch">
                <a href="index.php?<?= $_GET['id'] ? 'id=' . $_GET['id'] . '&' : '' ?>tasks_all=1<?= $show_complete_tasks ? '&show_completed=1' : '' ?>" class="tasks-switch__item <?= $_GET['tasks_all'] ? 'tasks-switch__item--active' : '' ?>">Все задачи</a>
                <a href="index.php?<?= $_GET['id'] ? 'id=' . $_GET['id'] . '&' : '' ?>tasks_today=1<?= $show_complete_tasks ? '&show_completed=1' : '' ?>" class="tasks-switch__item <?= $_GET['tasks_today'] ? 'tasks-switch__item--active' : '' ?>">Повестка дня</a>
                <a href="index.php?<?= $_GET['id'] ? 'id=' . $_GET['id'] . '&' : '' ?>tasks_tomorrow=1<?= $show_complete_tasks ? '&show_completed=1' : '' ?>" class="tasks-switch__item <?= $_GET['tasks_tomorrow'] ? 'tasks-switch__item--active' : '' ?>">Завтра</a>
                <a href="index.php?<?= $_GET['id'] ? 'id=' . $_GET['id'] . '&' : '' ?>tasks_old=1<?= $show_complete_tasks ? '&show_completed=1' : '' ?>" class="tasks-switch__item <?= $_GET['tasks_old'] ? 'tasks-switch__item--active' : '' ?>">Просроченные</a>
            </nav>

            <label class="checkbox">
                <input class="checkbox__input visually-hidden show_completed <?= $show_complete_tasks ? 'checked' : '' ?>" type="checkbox" <?= $show_complete_tasks ? 'checked' : '' ?>>
                <span class="checkbox__text">Показывать выполненные</span>
            </label>
        </div>

        <?= $not_found; ?>

        <table class="tasks">

            <?php

            // Выводим сообщение если нет задач в проекте
            foreach ($projects as $key => $value) {
                if (($_GET['id'] == $value['proj_id']) && !$value['count']) {
                    echo '<span style="font-size: 16px; font-weight: bold;">Нет задач для этого проекта</span>';
                }
            }

            // Соберем новый одномерный масив со значением proj_id
            foreach ($projects as $key => $value) {
                $valid_id[] = $value['proj_id'];
            }

            // Валидация proj_id, отправка заголовка 404 если proj_id = false
            if (!empty($_GET['id']) && !in_array($_GET['id'], $valid_id)) {
                header("HTTP/1.1 404 Not Found");
                print($page404);
            };

            // Если запрос присутсвует в форме поиска, то выводим данные поиска
            if (!empty($search)) {
                $tasks_list = $res_search;
            }

            // Вывод всех задач
            if ($tasks_list) :
                foreach ($tasks_list as $value) :

                    if (!$show_complete_tasks && $value['status_task']) {
                        continue;
                    }

                    $task_class = '';

                    // Запишем количество дней в переменную
                    $date = dateTask($value['date_task_end']);

                    // Проверим дату от пользователя с текущей (огонь если текущая дата или уже прошла)
                    if ($date && $date <= -1) {
                        $task_class .= 'task--important';
                    } else {
                        $task_class = '';
                    }
            ?>

                    <tr class="tasks__item task <?= $value['status_task'] ? 'task--completed' : $task_class;  ?>">
                        <td class="task__select">
                            <label class="checkbox task__checkbox">
                                <input class="checkbox__input visually-hidden task__checkbox" type="checkbox" <?= $value['status_task'] ? 'checked' : ''; ?>>
                                <a class="checkbox__text" href="index.php?task_complate=<?= $value['status_task'] ? 0 : 1; ?>&id_task=<?= $value['task_id'] ?>"><?= htmlspecialchars($value['title_task']); ?></a>
                            </label>
                        </td>

                        <td class="task__file">
                            <?php if (isset($value['link_file'])) : ?>
                                <a class="download-link" href="<?= $value['link_file'] ?>" download=""><?= end(explode('/', $value['link_file'])) ?></a>
                            <?php endif; ?>
                        </td>

                        <td class="task__date">
                            <?php if (isset($value['date_task_end'])) {
                                echo date('Y-m-d', strtotime($value['date_task_end']));
                            }
                            ?>
                        </td>
                    </tr>

            <?php endforeach;
            endif; ?>

        </table>
    </main>
</div>
