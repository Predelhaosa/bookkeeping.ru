<?php 
	require "libs/rb.php"; // Подключаем библиотеку RedBean PHP
	R::setup( 'mysql:host=localhost;dbname=bookkeeping.ru', 'root', '' ); // Подключаемся к базе данных с помошью PDO RedBean PHP (система подготовленных запросов)
	session_start(); //Запускаем сессию для далнейшего запоминания пользователя
?>