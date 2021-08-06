<?php
	require 'includes/db.php';								//Подключаем файл подключения к базе данных
  
	$data = $_POST; 										// Добавляем переменную и вносим в нее данные переданные методом POST из импутов
	$errors = array(); 										// Добавляем переменную для хранения ошибок и обьявляем ее массивом
  
	if (isset($data['do_signup'])) {
		if (trim($data['email']) == '') { 	// Удаляем пробелы из ячейки Email и проверяем не пуста ли она
				$errors['email'] = 'Введите Email!'; 	// Добавляем ошибку о том что не заполнено поле Email
		}
		if (R::count('users', "email = ?", array(trim($data['email']))) > 0 ) {
				$errors['email'] = 'Данный Email занят!';
		}
		if (trim($data['login']) == '') { 	// Удаляем пробелы из ячейки login и проверяем не пуста ли она
		$errors['login'] = 'Введите Login!'; 	// Добавляем ошибку о том что не заполнено поле login
		}
		if (R::count('users', "login = ?", array(trim($data['login']))) > 0 ) {
		$errors['login'] = 'Данный Login занят!';
		}
		if ($data['password'] == '') { 	// Проверяем не пуста ли ячейка Пароль. функцию trim() не приминяем так как пользователь вправе в поле пароль использовать пробелы
			$errors['password'] = 'Введите Пароль!'; 	// Добавляем ошибку о том что не заполнено поле password
		}
		else if (iconv_strlen($data['password']) < 8) { //Считаем количество внесенных символов в импут password если оно мешье 8 то выполниться тело IF конструкции
			$errors['password'] = 'Пароль должен содержать более 8-ми символов!'; 	// Добавляем ошибку о том что пароль должен содержать более 8-ми символов
		}
		if ($data['password_2'] == '') { 	// Проверяем не пуста ли ячейка password_2. По анологии с предыдущим полем функцию trim() не используем
			$errors['password_2'] = 'Введите повторный пароль!'; 	// Добавляем ошибку о том что не заполнено поле password_2
		}
		else if ($data['password_2'] != $data['password']) {
			$errors['password_2'] = 'Пароли не совпадают!'; 	// Добавляем ошибку о том что полу password и passwor_2 не совпадают
		}
		if (trim($data['phone']) == '') { 	// Удаляем пробелы из ячейки phone и проверяем не пуста ли она
			$errors['phone'] = 'Введите номер телефона!'; 	// Добавляем ошибку о том что не заполнено поле phone
		}
		if (trim($data['first_name']) == '') { 	// Удаляем пробелы из ячейки first_name и проверяем не пуста ли она
			$errors['first_name'] = 'Введите вашу фамилию!'; 	// Добавляем ошибку о том что не заполнено поле first_name
		}
		if (trim($data['name']) == '') { 	// Удаляем пробелы из ячейки name и проверяем не пуста ли она
			$errors['name'] = 'Введите Имя!'; 	// Добавляем ошибку о том что не заполнено поле name
		}
		if (trim($data['patronymic']) == '') { 	// Удаляем пробелы из ячейки patronymic и проверяем не пуста ли она
			$errors['patronymic'] = 'Введите отчество!'; 	// Добавляем ошибку о том что не заполнено поле patronymic
		}
		if ($data['year'] == '') { 		// Проверяем не пуста ли ячейка year
			$errors['year'] = 'Введите дату рождения!'; 	// Добавляем ошибку о том что не заполнено поле year
		}
		if ($data['type'] == '') { 		// Проверяем не пуста ли ячейка type
			$errors['type'] = 'Выберете тип учетной записи!'; 	// Добавляем ошибку о том что не заполнено поле year
		}
		if (empty($errors)) { 	//Если  масив с ошибками пуст то выполняем внесение данных в таблицу
 			$users = R::dispense('users'); //Подключаемся к таблице users
 			$users -> login = $data['login']; 	// Вводим значение в столбец login 
 			$users -> email = $data['email']; 	// Вводим значение в столбец Email
 			$users -> password = password_hash($data['password'], PASSWORD_DEFAULT);
 			$users -> phone = $data['phone'];	// Вводим значение в столбец Phone
 			$users -> first_name = $data['first_name'];	// Вводим значение в столбец First_name
 			$users -> name = $data['name'];	// Вводим значение в столбец Name
 			$users -> patronymic = $data['patronymic']; // Вводим значение в столбец Patronymic
 			$users -> year = $data['year']; // Вводим значение в столбец Year
 			$users -> type = $data['type']; // Вводим значение в столбец Type
 			R::store($users); // Сохраняем таблицу
 			$_SESSION['sign_up'] = 'true'; //Присваем ячейки sign_up значение true для использования на глвной странце что бы узнать что пользователь только что зарегестрировался
 			header('location: index.php'); //Переадресуем пользователя на главную страницу
 		}
	}
  ?>

 <!DOCTYPE html>
 <html lang="ru">
 <head>
 	<title>Регистрация</title>
 	<link rel="stylesheet" type="text/css" href="style.css">
 </head>
 <body>
 	<div>
 		<p><h1>Регистрация нового пользователя.</h1></p>
 	<form action="signup.php" method="POST">
 		<p>Введите электронный адрес (Email).</p>
 		<input type="email" name="email" value="<?php echo @$data['email'] ?>"> <!-- При ошибке скрипта возвращаем в инпуты внесенный раннее даные -->

		<?php 
			if (isset($errors['email'])) {
				echo "<div class='errors'>".$errors['email']."</div>";	// Выводим ошибку с права от импута Email
			}
			else echo "<br>"; // Добавляем, убранный через CSS свойство, перенос на новую строку
 		?> 

 		<p>Введите ваш логин.</p>
 		<input type="text" name="login" value="<?php echo @$data['login'] ?>">	<!-- При ошибке скрипта возвращаем в инпуты внесенный раннее даные -->

		<?php 
			if (isset($errors['login'])) { 
				echo "<div class='errors'>".$errors['login']."</div>";	// Выводим ошибку с права от импута Email
			}
			else echo "<br>";	// Добавляем, убранный через CSS свойство, перенос на новую строку
 		?>

		<p>Введите ваш пароль.</p>
 		<input type="password" name="password" >	<!-- В поле password ранее введенные данные не вносим -->

		<?php 
		if (isset($errors['password'])) { 
			echo "<div class='errors'>".$errors['password']."</div>";	 // Выводим ошибку с права от импута password
		}
		else echo "<br>";	// Добавляем, убранный через CSS свойство, перенос на новую строку
		?>

 		<p>Введите ваш пароль повторно.</p>
 		<input type="password" name="password_2"> <!-- Как и с полем password повторные данные мы не вносим -->

		<?php 
		if (isset($errors['password_2'])) { 
			echo "<div class='errors'>".$errors['password_2']."</div>";	
		}
		else echo "<br>";	// Добавляем, убранный через CSS свойство, перенос на новую строку
 		?> 

 		<p>Ваш номер мобильного телефона. (в формате 89183223232)</p>
 		<input type="tel" pattern="8[0-9]{10}" name="phone" value="<?php echo @$data['phone'] ?>"> 	<!-- При ошибке скрипта возвращаем в инпуты внесенный раннее даные -->

		<?php 
		if (isset($errors['phone'])) { 
			echo "<div class='errors'>".$errors['phone']."</div>";	// Выводим ошибку с права от импута phone
		}
		else echo "<br>";	// Добавляем, убранный через CSS свойство, перенос на новую строку
 		?>

 		<p>Ваша фамилия.</p>
 		<input type="text" name="first_name" value="<?php echo @$data['first_name'] ?>">	<!-- При ошибке скрипта возвращаем в инпуты внесенный раннее даные -->

		<?php 
		if (isset($errors['first_name'])) { 
				echo "<div class='errors'>".$errors['first_name']."</div>";		// Выводим ошибку с права от импута first_name
		}
		else echo "<br>";	// Добавляем, убранный через CSS свойство, перенос на новую строку
 		?>

 		<p>Ваше имя.</p>
 		<input type="text" name="name" value="<?php echo @$data['name'] ?>"> <!-- При ошибке скрипта возвращаем в инпуты внесенный раннее даные -->
 		
 		<?php 
 		if (isset($errors['name'])) { 
			echo "<div class='errors'>".$errors['name']."</div>";	// Выводим ошибку с права от импута name
		}
		else echo "<br>";	// Добавляем, убранный через CSS свойство, перенос на новую строку
 		?>

 		<p>Ваше отчество.</p>
 		<input type="text" name="patronymic" value="<?php echo @$data['patronymic'] ?>">	<!-- При ошибке скрипта возвращаем в инпуты внесенный раннее даные -->

 		<?php 
 		if (isset($errors['patronymic'])) { 
			echo "<div class='errors'>".$errors['patronymic']."</div>";		// Выводим ошибку с права от импута patronymic
		}
		else echo "<br>";	// Добавляем, убранный через CSS свойство, перенос на новую строку
 		?>

 		<p>Ваша дата рождения.</p>
 		<input type="date" min="1900-01-01" max="<?php echo date("Y-m-d")?>" name="year" value="<?php echo @$data['year'] ?>"> <!-- Ограничиваем выбор даты начиная с 01.01.1900 по сегодняшний день. При ошибке скрипта возвращаем в инпуты внесенный раннее даные -->

		<?php 
		if (isset($errors['year'])) { 
			echo "<div class='errors'>".$errors['year']."</div>";	// Выводим ошибку с права от импута year
		}
		else echo "<br>";	// Добавляем, убранный через CSS свойство, перенос на новую строку
		if ($_SESSION['logged_user']->type == 'admin' or $_SESSION['logged_user']->type == 'super_user') {
 			echo "<p>Выберете тип учетной записи</p>
 			<input type='radio' name='type' value='admin'>Администратор<br>
 			<input type='radio' name='type' value='employee'>Сотрудник<br>
 			<input type='radio' name='type' value='user'> Пользователь <br>";
 			if (isset($errors['type'])) { 
				echo "<div class='errors'>".$errors['type']."</div>";	// Выводим ошибку с права от импута year
			}
 		}
 		else echo "<input type='hidden' name='type' value='user'>"
 		?>

 		<p><button type="submit" name="do_signup">Зарегистрировать</button></p>	
 	</form>
 	<a href="index.php">Вернуться на главную</a>
 	</div>
 </body>
 </html>