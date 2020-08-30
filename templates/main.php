<div class="content">
    <section class="content__side">

        <h2 class="content__side-heading">Проекты</h2>

        <div id="projects">
            <nav class="main-navigation">

                <?php foreach ($projects as $key => $value) : ?>

                    <ul class="main-navigation__list">
                        <li class="main-navigation__list-item <?= $get_id === $value['proj_id'] ? 'main-navigation__list-item--active' : '' ?>">
                            <a class="main-navigation__list-item-link" href="<?= 'index.php?id=' . $value['proj_id'] ?>"><?= htmlspecialchars($value['proj_name']); ?></a>
                            <span class="main-navigation__list-item-count"><?= $value['count']; ?></span>
                        </li>
                    </ul>

                <?php endforeach; ?>

            </nav>
        </div>

        <a class="button button--transparent button--plus content__side-button" href="/proj.php">Добавить проект</a>

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

                <a href="index.php?<?= $get_id ? 'id=' . $get_id . '&' : '' ?>all=1" class="tasks-switch__item <?= $get_all ? 'tasks-switch__item--active' : '' ?>">Все задачи</a>

                <a href="index.php?<?= $get_id ? 'id=' . $get_id . '&' : '' ?>today=1" class="tasks-switch__item <?= $get_today ? 'tasks-switch__item--active' : '' ?>">Повестка дня</a>

                <a href="index.php?<?= $get_id ? 'id=' . $get_id . '&' : '' ?>tomorrow=1" class="tasks-switch__item <?= $get_tomorrow ? 'tasks-switch__item--active' : '' ?>">Завтра</a>

                <a href="index.php?<?= $get_id ? 'id=' . $get_id . '&' : '' ?>old=1" class="tasks-switch__item <?= $get_old ? 'tasks-switch__item--active' : '' ?>">Просроченные</a>

            </nav>

            <label class="checkbox">
                <input class="checkbox__input visually-hidden show_completed <?= $show_completed_tasks ? 'checked' : '' ?>" type="checkbox" <?= $show_completed_tasks ? 'checked' : '' ?>>
                <span class="checkbox__text">Показывать выполненные</span>
            </label>
        </div>

        <?= $not_found; ?>

        <table class="tasks">

            <?php

            // Выводим сообщения
            foreach ($projects as $project => $value) {

                // Выводим сообщение если нет задач в проекте
                if (($get_id === $value['proj_id']) && !$value['count']) {
                    echo '<span style="font-size: 16px; font-weight: bold;">Нет задач для этого проекта</span>';
                }

                // Выводим сообщение если нет задач в фильтре
                if (($get_id === $value['proj_id']) && $value['count'] && !$tasks_list) {
                    echo '<span style="font-size: 16px; font-weight: bold;">Нет задач для этого фильтра</span>';
                }
            }

            // Если запрос присутствует в форме поиска, то выводим
            if (!empty($search)) {
                $tasks_list = $result_search;
            }

            // Вывод всех задач
            if ($tasks_list) :

                // Смещение по ключу в массиве задач (пагинация)
                $offset_key = $task_one_page;

                if ($cur_page) {
                    // Вывод задач с пагинацией
                    $output_tasks_list = array_slice($tasks_list, (($cur_page - 1) * 3), $offset_key, $preserve_keys = TRUE);
                } else {
                    // Вывод задач без пагинации
                    $output_tasks_list = array_slice($tasks_list, ($cur_page * 3), $offset_key, $preserve_keys = TRUE);
                }

                // Перепишем основной массив задач с учетом пагинации
                $tasks_list = $output_tasks_list;

                foreach ($tasks_list as $key => $value) :

                    if (!$show_completed_tasks && !empty($value['status_task'])) {
                        continue;
                    }

                    // Значение класса по умолчанию
                    $task_class = '';

                    // Запишем количество дней в переменную
                    $date = dateTask($value['date_task_end']);

                    // Проверим дату от пользователя с текущей (огонь если текущая дата или прошла)
                    if ($date && $date <= -1) {
                        $task_class .= 'task--important';
                    } else {
                        $task_class = '';
                    }

            ?>

                    <tr class="tasks__item task <?= $value['status_task'] ? 'task--completed' : $task_class;  ?>">
                        <td class="task__select">
                            <label class="checkbox task__checkbox">
                                <input class="checkbox__input visually-hidden task__checkbox" type="checkbox" <?= !empty($value['status_task']) ? 'checked' : ''; ?>>
                                <a class="checkbox__text" href="index.php?task_completed=<?= $get_task_completed; ?>&id_task=<?= $value['task_id'] ?><?= $show_completed_tasks ? '&show_completed=1' : '' ?>"><?= htmlspecialchars($value['title_task']); ?></a>
                            </label>
                        </td>

                        <td class="task__file">
                            <?php if (!empty($value['link_file'])) : ?>
                                <a class="download-link" href="<?= $value['link_file'] ?>" download=""><?= str_replace('uploads/', '', $value['link_file']) ?></a>
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

        <!-- Pagination -->
        <?php if ((empty($_GET['q'])) && ($all_tasks > $task_one_page) && (!empty($tasks_list))) : ?>

            <div class="tasks-pagination">
                <nav aria-label="Page navigation">
                    <ul class="pagination">

                        <li class="page-item">
                            <a class="page-link" href="index.php?<?= $get_id ? 'id=' . $get_id . '&page=' . $pages_prev : 'page=' . $pages_prev; ?><?= $active_filter_link; ?><?= $show_completed_tasks ? '&show_completed=1' : '' ?>" aria-label="Previous">
                                <span aria-hidden="true">&laquo;</span>
                                <span class="sr-only">Назад</span>
                            </a>
                        </li>

                        <?php foreach ($pages as $page) : ?>

                            <li class="page-item <?= ($page === $cur_page) ? 'active' : '' ?>">

                                <a class="page-link" href="index.php?<?= $get_id ? 'id=' . $get_id . '&page=' . $page : 'page=' . $page ?><?= $active_filter_link; ?><?= $show_completed_tasks ? '&show_completed=1' : '' ?>"><?= $page; ?></a>

                            </li>

                        <?php endforeach; ?>

                        <li class="page-item">
                            <a class="page-link" href="index.php?<?= $get_id ? 'id=' . $get_id . '&page=' . $pages_next  : 'page=' . $pages_next; ?><?= $active_filter_link; ?><?= $show_completed_tasks ? '&show_completed=1' : '' ?>" aria-label="Next">
                                <span class="sr-only">Вперед</span>
                                <span aria-hidden="true">&raquo;</span>
                            </a>
                        </li>

                    </ul>
                </nav>
            </div>

        <?php endif; ?>

        <!-- //Pagination -->

    </main>

</div>
