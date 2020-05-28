<?php

// массив категорий
	$categories = ['Входящие', 'Учеба', 'Работа', 'Домашние дела', 'Авто'];

//массивы с задачами
$first_task = [
	'title_task'    => 'Собеседование в IT компании',
	'date_task'     => '01.12.2019',
	'cat_task'      => 'Работа',
	'status_task'   => false,
];

$second_task = [
	'title_task'    => 'Выполнить тестовое задание',
	'date_task'     => '25.12.2019',
	'cat_task'      => 'Работа',
	'status_task'   => false,
];

$third_task = [
	'title_task'    => 'Сделать задание первого раздела',
	'date_task'     => '21.12.2019',
	'cat_task'      => 'Учеба',
	'status_task'   => true,
];

$fourth_task = [
	'title_task'    => 'Встреча с другом',
	'date_task'     => '22.12.2019',
	'cat_task'      => 'Входящие',
	'status_task'   => false,
];

$fifth_task = [
	'title_task'    => 'Купить корм для кота',
	'date_task'     => null,
	'cat_task'      => 'Домашние дела',
	'status_task'   => false,
];

$sixth_task = [
	'title_task'    => 'Заказать пиццу',
	'date_task'     => null,
	'cat_task'      => 'Домашние дела',
	'status_task'   => false,
];

$tasks_list = [$first_task, $second_task, $third_task, $fourth_task, $fifth_task, $sixth_task];

?>
