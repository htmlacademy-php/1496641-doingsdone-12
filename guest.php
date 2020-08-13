<?php

require_once('functions.php');
require_once('data.php');

/**
 *
 * * СОБИРАЕМ ШАБЛОН - ГОСТЬ
 */

// Данные для передачи в шаблон
$guest_data = [];

// Данные для шаблона
$content_reg = include_template('guest.php', $guest_data);

// Шаблон страницы
$layout = include_template('layout.php', [
    'content'   =>  $content_reg,
]);

print($layout);
