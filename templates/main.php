<div class="content">
    <section class="content__side">

        <h2 class="content__side-heading">Проекты</h2>

        <div id="projects">
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
                <a href="index.php?<?= $_GET['id'] ? 'id=' . $_GET['id'] : '' ?>&all=1<?= $show_completed_tasks ? '&show_completed=1' : '' ?>" class="tasks-switch__item <?= $_GET['all'] ? 'tasks-switch__item--active' : '' ?>">Все задачи</a>

                <a href="index.php?<?= $_GET['id'] ? 'id=' . $_GET['id'] : '' ?>&today=1<?= $show_completed_tasks ? '&show_completed=1' : '' ?>" class="tasks-switch__item <?= $_GET['today'] ? 'tasks-switch__item--active' : '' ?>">Повестка дня</a>

                <a href="index.php?<?= $_GET['id'] ? 'id=' . $_GET['id'] : '' ?>&tomorrow=1<?= $show_completed_tasks ? '&show_completed=1' : '' ?>" class="tasks-switch__item <?= $_GET['tomorrow'] ? 'tasks-switch__item--active' : '' ?>">Завтра</a>

                <a href="index.php?<?= $_GET['id'] ? 'id=' . $_GET['id'] : '' ?>&old=1<?= $show_completed_tasks ? '&show_completed=1' : '' ?>" class="tasks-switch__item <?= $_GET['old'] ? 'tasks-switch__item--active' : '' ?>">Просроченные</a>
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
                if (($_GET['id'] == $value['proj_id']) && !$value['count']) {
                    echo '<span style="font-size: 16px; font-weight: bold;">Нет задач для этого проекта</span>';
                }

                // Выводим сообщение если нет задач в фильтре
                if (($_GET['id'] == $value['proj_id']) && $value['count'] && !$tasks_list) {
                    echo '<span style="font-size: 16px; font-weight: bold;">Нет задач для этого фильтра</span>';
                }
            }

            // Соберем новый одномерный массив со значением id проектов
            foreach ($projects as $key => $value) {
                $valid_id[] = $value['proj_id'];
            }

            // Валидация proj_id, отправка заголовка 404 если proj_id = false
            if (!empty($_GET['id']) && !in_array($_GET['id'], $valid_id)) {
                header("HTTP/1.1 404 Not Found");
                print($page404);
            };

            // Если запрос присутствует в форме поиска, то выводим данные поиска
            if (!empty($search)) {
                $tasks_list = $res_search;
            }

            // Вывод всех задач
            if ($tasks_list) :

                // Смещение по ключу в массиве задач
                $offset_key = $task_one_page;

                if ($_GET['page']) {
                    // Вывод задач на странице с учетом пагинации
                    $output_tasks_list = array_slice($tasks_list, (($_GET['page'] - 1) * 3), $offset_key, $preserve_keys = TRUE);
                } else {
                    // Вывод задач без пагинации
                    $output_tasks_list = array_slice($tasks_list, ($_GET['page'] * 3), $offset_key, $preserve_keys = TRUE);
                }
                // Перепишем основной массив задач с учетом пагинации
                $tasks_list = $output_tasks_list;

                foreach ($tasks_list as $key => $value) :

                    if (!$show_completed_tasks && $value['status_task']) {
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
                                <a class="checkbox__text" href="index.php?task_completed=<?= $get_task_completed; ?>&id_task=<?= $value['task_id'] ?><?= $show_completed_tasks ? '&show_completed=1' : '' ?>"><?= htmlspecialchars($value['title_task']); ?></a>
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

        <!-- Pagination -->
        <?php if (($all_tasks > $task_one_page) && (!empty($tasks_list))) : ?>

            <div class="tasks-pagination">
                <nav aria-label="Page navigation">
                    <ul class="pagination">

                        <?php

                        // Запишем ключи массива $_GET для фильтров в новый массив
                        $filters = ['all', 'today', 'tomorrow', 'old'];

                        // Переберем массив $filters
                        foreach ($filters as $key) {
                            // При совпадении значения массива $filters с ключом массива $_GET
                            // Формируем ссылку на активный фильтр
                            if ($_GET[$key]) {
                                $filter = '&' . $key . '=1';
                            }
                        }

                        // Предыдущая страница
                        if ($cur_page > 1) {
                            $pages_prev = $cur_page - 1;
                        } else {
                            $pages_prev = 1;
                        }

                        // Следующая страница
                        if ($cur_page < $pages_count) {
                            $pages_next = $cur_page + 1;
                        } else {
                            $pages_next = $pages_count;
                        }

                        ?>

                        <li class="page-item">
                            <a class="page-link" href="<?= $_GET['id'] ? '?id=' . $_GET['id'] . '&page=' . $pages_prev : '?page=' . $pages_prev; ?><?= $filter; ?><?= $show_completed_tasks ? '&show_completed=1' : '' ?>" aria-label="Previous">
                                <span aria-hidden="true">&laquo;</span>
                                <span class="sr-only">Назад</span>
                            </a>
                        </li>

                        <?php foreach ($pages as $page) : ?>

                            <li class="page-item <?= ($page == $cur_page) ? 'active' : '' ?>">
                                <a class="page-link" href="<?= $_GET['id'] ? '?id=' . $_GET['id'] . '&page=' . $page : '?page=' . $page ?><?= $filter; ?><?= $show_completed_tasks ? '&show_completed=1' : '' ?>"><?= $page; ?></a>
                            </li>

                        <?php endforeach; ?>

                        <li class="page-item">
                            <a class="page-link" href="<?= $_GET['id'] ? '?id=' . $_GET['id'] . '&page=' . $pages_next  : '?page=' . $pages_next; ?><?= $filter; ?><?= $show_completed_tasks ? '&show_completed=1' : '' ?>" aria-label="Next">
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
