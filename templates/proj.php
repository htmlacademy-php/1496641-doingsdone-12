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
        <a class="button button--transparent button--plus content__side-button" href="/proj.php" target="project_add">Добавить проект</a>
    </section>

    <main class="content__main">
        <h2 class="content__main-heading">Добавление проекта</h2>

        <form class="form" action="proj.php" method="post" autocomplete="off">
            <div class="form__row">
                <label class="form__label" for="project_name">Название <sup>*</sup></label>

                <input class="form__input <?= $errors['project_name'] ? 'form__input--error' : '' ?>" type="text" name="project_name" id="project_name" value="" placeholder="Введите название проекта">
                <?= $errors['project_name'] ? '<p class="form__message">' . $errors['project_name'] . '</p>' : ''  ?>
            </div>

            <div class="form__row form__row--controls">
                <input class="button" type="submit" name="" value="Добавить">
            </div>
        </form>
    </main>
</div>