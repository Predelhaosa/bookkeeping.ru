<?php 
require 'includes/db.php'; // Подключаем файл с подключение базы
unset($_SESSION['logged_user']); // Отчищаем ячейку залогининого пользователя
header('location: index.php');	// Переадресуем пользователя на главную страницу
?>