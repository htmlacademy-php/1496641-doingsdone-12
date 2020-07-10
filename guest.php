<?php

require_once('functions.php');
require_once('data.php');


// TODO СОБИРАЕМ ШАБЛОН - РЕГИСТРАЦИЯ НА САЙТЕ

// Данные для передачи в шаблон
$guest_data = [];

// Контентная часть
$content_reg = include_template('guest.php', $guest_data);

// Шаблон страницы
$layout = include_template('layout.php', [
    'content'   =>  $content_reg,
    // 'title'     => 'Document',
]);

print($layout);
