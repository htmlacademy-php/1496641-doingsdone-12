<?php

// Счетчик задач в блоке категорий
function counTasksInCat($arr_tasks_list, $str_cat)
{
	$i = 0;
	foreach ($arr_tasks_list as $val) {
		if ($val['cat_task'] === $str_cat) {
			$i += 1;
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
function include_template($name, array $data = [])
{
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

/**
 * Подсчет количества дней до выполнения задачи
 * @param string $task_end дата выполнения задачи
 * @return string итоговый результат - количество дней до выполнения задачи
 */

function dataTask($task_end)
{
	$secs_in_day = 86400; // 24 часа = 86400 секунд
	$now_ts = time(); //текущая метка timestamp
	$end_ts = strtotime($task_end); // дата выполнения задачи timestamp
	$ts_diff = floor(($end_ts - $now_ts) / $secs_in_day); // количество оставшихся дней до выполнения задачи
	return $ts_diff;
}
