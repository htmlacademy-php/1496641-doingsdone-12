<div class="content">
	<section class="content__side">
		<h2 class="content__side-heading">Проекты</h2>

		<nav class="main-navigation">

			<?php foreach ($categories as $cat) : ?>
				<ul class="main-navigation__list">
					<li class="main-navigation__list-item">
						<a class="main-navigation__list-item-link" href="#"><?= htmlspecialchars($cat) ?></a>
						<span class="main-navigation__list-item-count"><?= counTasksInCat($tasks_list, $cat); ?></span>
					</li>
				</ul>
			<?php endforeach; ?>
		</nav>

		<a class="button button--transparent button--plus content__side-button" href="pages/form-project.html" target="project_add">Добавить проект</a>
	</section>

	<main class="content__main">
		<h2 class="content__main-heading">Список задач</h2>

		<form class="search-form" action="index.php" method="post" autocomplete="off">
			<input class="search-form__input" type="text" name="" value="" placeholder="Поиск по задачам">

			<input class="search-form__submit" type="submit" name="" value="Искать">
		</form>

		<div class="tasks-controls">
			<nav class="tasks-switch">
				<a href="/" class="tasks-switch__item tasks-switch__item--active">Все задачи</a>
				<a href="/" class="tasks-switch__item">Повестка дня</a>
				<a href="/" class="tasks-switch__item">Завтра</a>
				<a href="/" class="tasks-switch__item">Просроченные</a>
			</nav>

			<label class="checkbox">
				<!--добавить сюда атрибут "checked", если переменная $show_complete_tasks равна единице-->
				<input class="checkbox__input visually-hidden show_completed <?= $show_complete_tasks ? 'checked' : '' ?>" type="checkbox">
				<span class="checkbox__text">Показывать выполненные</span>
			</label>
		</div>

		<table class="tasks">

			<?php foreach ($tasks_list as $value) :
				if (!$show_complete_tasks && $value['status_task']) {
					continue;
				}  ?>

				<tr class="tasks__item task <?= $value['status_task'] ? 'task--completed' : '' ?>">
					<td class="task__select">
						<label class="checkbox task__checkbox">
							<input class="checkbox__input visually-hidden task__checkbox" type="checkbox" <?= $value['status_task'] ? 'checked' : '' ?>>
							<span class="checkbox__text"><?= htmlspecialchars($value['title_task']); ?></span>
						</label>
					</td>

					<td class="task__file">
						<a class="download-link" href="#">Home.psd</a>
					</td>

					<td class="task__date"><?= $value['date_task'] ?></td>
				</tr>

			<?php endforeach; ?>

		</table>
	</main>
</div>