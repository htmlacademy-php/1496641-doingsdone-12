<div class="content">
    <section class="content__side">
        <h2 class="content__side-heading">Проекты</h2>

        <nav class="main-navigation">
            <?php foreach ($projects as $project) : ?>
                <ul class="main-navigation__list">
                    <li class="main-navigation__list-item <?= $_GET['id'] == $project['proj_id'] ? 'main-navigation__list-item--active' : '' ?>">
                        <a class="main-navigation__list-item-link" href="<?= 'index.php?id=' . $project['proj_id'] ?>"><?= htmlspecialchars($project['proj_name']) ?></a>
                        <span class="main-navigation__list-item-count"><?= countTask($count_tasks, $project['proj_name']); ?></span>
                    </li>
                </ul>
            <?php endforeach; ?>
        </nav>

        <a class="button button--transparent button--plus content__side-button" href="pages/form-project.html" target="project_add">Добавить проект</a>
    </section>

    <main class="content__main">
        <h2 class="content__main-heading">Добавление задачи</h2>

        <form class="form" action="add.php" method="post" autocomplete="off" enctype="multipart/form-data">
            <div class="form__row">
                <label class="form__label" for="name">Название <sup>*</sup></label>

                <input class="form__input <?= $errors['name'] ? 'form__input--error' : '' ?>" type="text" name="name" id="name" value="<?= postValue($_POST['name']); ?>" placeholder="Введите название">

                <?= $errors['name'] ? '<p class="form__message">' . $errors['name'] . '</p>' : ''  ?>
            </div>

            <div class="form__row">
                <label class="form__label" for="project">Проект <sup>*</sup></label>

                <select class="form__input form__input--select <?= $errors['project'] ? 'form__input--error' : '' ?>" name="project" id="project">

                    <option value="none" selected disabled hidden>Выбрать проект</option>
                    <?php foreach ($projects as $project) : ?>
                        <option <?= ($project_id === $project['proj_id']) ? 'selected' : '' ?> value="<?= $project['proj_id']; ?>"><?= $project['proj_name']; ?></option>
                    <?php endforeach; ?>

                </select>

                <?= $errors['project'] ? '<p class="form__message">' . $errors['project'] . '</p>' : ''  ?>
            </div>

            <div class="form__row">
                <label class="form__label" for="date">Дата выполнения</label>

                <input class="form__input form__input--date <?= $errors['date'] ? 'form__input--error' : '' ?>" type="text" name="date" id="date" value="<?= postValue($_POST['date']); ?>" placeholder="Введите дату в формате ГГГГ-ММ-ДД">

                <?= $errors['date'] ? '<p class="form__message">' . $errors['date'] . '</p>' : ''  ?>
            </div>

            <div class="form__row">
                <label class="form__label" for="file">Файл</label>

                <div class="form__input-file">
                    <input class="visually-hidden" type="file" name="file" id="file" value="">

                    <label class="button button--transparent" for="file">
                        <span>Выберите файл</span>
                    </label>

                    <?= $errors['file'] ? '<p class="form__message">' . $errors['file'] . '</p>' : ''  ?>

                </div>
            </div>

            <div class=" form__row form__row--controls">
                <input class="button" type="submit" name="submit" value="Добавить">
            </div>
        </form>
    </main>
</div>