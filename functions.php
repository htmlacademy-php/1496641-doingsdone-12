<?php

/**
 * Счетчик задач в проекте
 * @param array $arr_tasks_list массив задачи с данными
 * @param string $str_cat название задачи
 * @return string количество задач в проекте
 */

// function countTasksInCat($arr_tasks_list, $str_cat)
// {
// 	$i = 0;
// 	foreach ($arr_tasks_list as $val) {
// 		if ($val['proj_name'] === $str_cat) {
// 			$i += 1;
// 		}
// 	}
// 	return $i;
// }


function countTask($arr_count_task, $str_cat)
{
	foreach ($arr_count_task as $key => $value) {
		if ($value['proj_name'] === $str_cat) {
			return $value['count_task'];
		}
	}
	return 0;
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

/**
 * Выводит результат запроса sql из указанной таблицы в виде массива
 * @param string $sql запрос к БД
 * @param string $sql_table таблица в БД к которой формируется запрос
 * @param array $connect ассоциативный массив с параметрами для подключения к БД
 * @return array массив значений сформированный на основании запроса $sql
 */

function resQuerySQL($sql, $connect)
{
	// получаем ресурс результата
	$result = mysqli_query($connect, $sql);

	// проверим результат извлечения данных
	if ($result) {
		$sql_table = mysqli_fetch_all($result, MYSQLI_ASSOC);
	}
	// возвращаем результат запроса в виде массива
	return $sql_table;
}
