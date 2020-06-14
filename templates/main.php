<div class="content">
	<section class="content__side">
		<h2 class="content__side-heading">Проекты</h2>

		<nav class="main-navigation">
			<?php foreach ($categories as $cat) : ?>
				<ul class="main-navigation__list">
					<li class="main-navigation__list-item <?= $_GET['id'] == $cat['proj_id'] ? 'main-navigation__list-item--active' : '' ?>">
						<a class="main-navigation__list-item-link" href="<?= 'index.php?id=' . $cat['proj_id'] ?>"><?= htmlspecialchars($cat['proj_name']) ?></a>
						<span class="main-navigation__list-item-count"><?= countTask($count_task, $cat['proj_name']); ?></span>

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
				<input class="checkbox__input visually-hidden show_completed <?= $show_complete_tasks ? 'checked' : '' ?>" type="checkbox">
				<span class="checkbox__text">Показывать выполненные</span>
			</label>
		</div>

		<table class="tasks">


			<?php

			// Выводим сообщение если нет задач в проекте 
			foreach ($count_tasks as $key => $value) {
				if (($_GET['id'] == $value['proj_id']) && !$value['count']) {
					echo '<span style="font-size: 16px; font-weight: bold;">Нет задач для этого проекта</span>';
				}
			}

			// Валидация $_GET[id] 
			if ($valid_id != $_GET['id']) {
				echo $page404;
			};

			?>


			<?php foreach ($task_list as $value) :
				if (!$show_complete_tasks && $value['status_task']) {
					continue;
				}

				$task_class = '';

				if ($value['status_task']) {
					$task_class = 'task--completed';
				}

				if (dataTask($value['date_task'])) {
					$task_class .= ' task--important';
				} ?>

				<tr class="tasks__item task <?= $task_class; ?>">
					<td class="task__select">
						<label class="checkbox task__checkbox">
							<input class="checkbox__input visually-hidden task__checkbox" type="checkbox" <?= $value['status_task'] ? 'checked' : '' ?>>
							<span class="checkbox__text"><?= htmlspecialchars($value['title_task']); ?></span>
						</label>
					</td>

					<td class="task__file">
						<?php if (isset($value['link_file'])) : ?>
							<a class="download-link" href="#">Home.psd</a>
						<?php endif; ?>
					</td>

					<td class="task__date">
						<?= date('d.m.Y', strtotime($value['date_task_end'])) ?>
					</td>
				</tr>

			<?php endforeach; ?>

		</table>
	</main>
</div>