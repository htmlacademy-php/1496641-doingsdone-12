<?php

// Счетчик задач в блоке категорий
function counTasksInCat($arr_tasks_list, $str_cat) {
	$i = 0;
	foreach ($arr_tasks_list as $val) {
		if ($val['cat_task'] === $str_cat) {
			$i+=1;
		}
	} 
	return $i;
}


/**
 * Подключает шаблон, передает туда данные и возвращает итоговый HTML контент
 * @param string $name Путь к файлу шаблона относительно папки templates
 * @param array $data Ассоциативный массив с данными для шаблона
 * @return string Итоговый HTML
 */
function include_template($name, array $data = []) {
	$name = 'templates/' . $name;
	$result = '';

	if (!is_readable($name)) {
		return $result;
	}

	ob_start();
	extract($data);
	require $name;

	$result = ob_get_clean();

	return $result;
}

