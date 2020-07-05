<?php

require_once('functions.php');
require_once('data.php');


// TODO СОБИРАЕМ ШАБЛОН - РЕГИСТРАЦИЯ НА САЙТЕ

// Данные для передачи в шаблон
$tpl_data = [
    // 'errors' => $errors,
    // 'warning' => $warning,
    // 'req_fields' => $req_fields,
    // 'form' => $form,
];

// Контентная часть
$content_reg = include_template('guest.php', $tpl_data);

// Шаблон страницы
$layout = include_template('layout.php', [
    'content'   =>  $content_reg,
    // 'title'     => 'Document',
]);

print($layout);
