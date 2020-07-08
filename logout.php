<?php
// Обнулим сессию и редирект на главную
session_start();
session_unset();
session_destroy();
header("Location: index.php");
