<?php  
    require 'includes/db.php'; // Подключаем файл подключения к БД
    $data = $_POST;            // Добавляем переменную и вносим в нее данные переданные методом POST из импутов
    $errors = array();         // Добавляем массив для ошибок
    $user = R::findOne('users', 'login = ?', array($data['login'])); // Делаем выборку из базы данных с условием которое введенно в поле login 
    $appointment_list = R::findall('appointment'); // Делаем выборку всех записей на приём
    if (isset($data['do_login'])) {  // Проверяем нажата ли кнопка войти
        if (trim($data['login']) == '') {   // Удаляем пробелы из ячейки Login и проверяем не пуста ли она
           $errors['login'] = 'Введите login';    // Добавляем ошибку о том что не заполнено поле Login
        }
        elseif ($user) { // Если по введенным пользователем данным нашлась строка в базе данных то выполниться код в теле IF конструкции
            if (password_verify($data['password'], $user->password)) { //Даная конструкция хеширует пароль введенный пользователем и сравнивает его с хешем из базы данных если они совпадают то выполниться код IF цикла
                 $_SESSION['logged_user'] = $user; //Заносим данные пользователя в глобальную переменную
                header('location: index.php');  //Обновляем страницу
            }
            else  $errors['password'] = 'Неверный пароль';    // Добавляем ошибку о том что введен неверный пароль
        }
        else $errors['login'] = 'Пользователь с таким Login не найден!'; // Если введенного пользователем логина нет в базе данных то добавляем ошибку
        if ($data['password'] == '') {  // Проверяем не пуста ли ячейка Пароль. функцию trim() не приминяем так как пользователь вправе в поле пароль использовать пробелы
            $errors['password'] = 'Введите Пароль!';   // Добавляем ошибку о том что не заполнено поле пароль
        }
    }
    if (isset($data['do_appointment'])) {   //Проверяем нажата ли кнопка записаться
        if ($data['employee'] == '') { //Если не выбран бухгалтер то добавляем ошибку в ячейку employee масcива errors
        $errors['employee'] = 'Выберите бугалтера!';
        }
        if ($data['date'] == '') { //Если не выбрана дата то добавляем ошибку в ячейку employee масcива errors
        $errors['date'] = 'Выберите дату приема!';
        }
        if ($data['time'] == '') { //Если не выбрано время то добавляем ошибку в ячейку employee масcива errors
        $errors['time'] = 'Выберите время приема!';
        }
        foreach ($appointment_list as $aappointment_list) { //построчно прокручиваем массив со всеми записями на приём
            if ($aappointment_list['employee'] == $data['employee'] and $aappointment_list['date'] == $data['date'] and $aappointment_list['time'] == $data['time']) { //Проверяем не занята эта дата и время у выбранного сотрудника
                $errors['appointment'] = 'Эта дата и время занято у данного сотрудника!'; // если все три вышеуказанные условия истина то добавляем ошибку о том что данное время и дата уже заняты у данного сотрудника
            }
            if ($aappointment_list['user_id'] == $_SESSION[logged_user]->id and $aappointment_list['date'] == $data['date'] and $aappointment_list['time'] == $data['time']) { //проверяем нет ли записи на эту дату и время у активного пользователя пользователя
                $errors['appointment'] = 'Вы уже записывались на эту дату и время'; // если все три вышеуказанные условия истина то добовляем ошибку о том что на данное время и дату пользователь уже записан
            }
         }
            
        
        if (empty($errors)) { // если ошибок при записи нет то добовляем запись
            $appointment = R::dispense('appointment'); //Подключаемся к таблице
            $appointment -> user_id = $_SESSION[logged_user]->id; //Вносим данные
            $appointment -> employee = $data['employee'];
            $appointment -> date = $data['date'];
            $appointment -> time = $data['time'];
            R::store($appointment); //Сохраняем изменения
            $_SESSION['appointment']='true'; //Добавляем ячейку об успешном удалении
            header('location: index.php'); //Обнавляем страницу
        }
        }
        if (isset($data['delete_appointment'])) { //Если нажата кнопка удаления записи, то исполниться тело IF цикла
            $delete = R::load('appointment', $data['id_appointment']); //загружаем запись соотвествующю выбранной пользователем
            R::trash($delete); // удаляем запись
            $_SESSION['delete_appointment']='true'; //Добавляем ячейку об успешной записи
            header('location: index.php'); // переодресация для обновления данных на странице
        }
        if (isset($data['delete_user'])) { //Если нажата кнопка удаления записи, то исполниться тело IF цикла
            $delete = R::load('users', $data['id_user']); //загружаем запись соотвествующю выбранной пользователем
            R::trash($delete); // удаляем запись
            $_SESSION['delete_user']='true'; //Добавляем ячейку об успешном удалении
            header('location: index.php'); // переодресация для обновления данных на странице
        }
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <title>Bookkeeping.ru</title>
    <link rel="stylesheet" href="style.css" type="text/css">
</head>
<body>
    <header>
        <section>
            <div class="logo">
                <p><h1>Bookkeeping</h1></p>
            </div>
        </section>
    </header>
    <section>
            <div class="signup">

            <?php
    if (empty($_SESSION['logged_user'])) : //Если пользователь не активен
            ?>
                <form action="index.php" method="POST">

                    <?php 
    if($_SESSION['sign_up']=='true'){ //Если есть ячейка об успешной регистрации
        echo "<div class='ok'>Вы успешно зарегестрировались!</div>";  //Выводим сообщение об успешной регистрации
        unset($_SESSION['sign_up']); //Удаляем ячейку
    }
                    ?>

                    <h3>Ваш логин.</h3>
                    <input type="text" name="login" value="<?php echo @$data['login'] ?>"> <!-- При ошибке вносим ранее введенные данные -->
        
                    <?php 
    if (isset($errors['login'])) {  //Если есть запись в ячейки Login массива errors
        echo "<div class='errors'>".$errors['login']."</div>";  // Выводим ошибку с права от импута Логин
    }
    else echo "<br>"; //Добовляем перенос строки
                    ?>

                    <h3>Ваш пароль.</h3>
                    <input type="password" name="password">

                    <?php 
    if (isset($errors['password'])) { //Если есть запись в ячейки Login массива errors
        echo "<div class='errors'>".$errors['password']."</div><br>"; // Выводим ошибку с права от импута Пароль
    }
    else echo "<br><br>"; //Добовляем перенос строки (перед кнопками требуеться два переноса для корректного отображения)
                    ?>

                    <button type="submit" name="do_login">Войти</button>
                </form>
                <p><a href="signup.php">Регистрация</a></p>
                </div>
                <?php 
    else : //Если пользователь активен
                ?>
                </section>
    <section>
        <div class="welcome">
                <h3>Добро пожаловать!</h3>

                <?php 
    echo $_SESSION['logged_user']->first_name." ".$_SESSION['logged_user']->name." ".$_SESSION['logged_user']->patronymic."<br><br><a href='logout.php'>Выход</a><br>"; // Вывод фамилии имя и отчество активного пользователя
    endif //Конец цикла который определяет активен ли пользователь
                ?>
                </div>
        <div class="conteiner">
        <?php 
    if ($_SESSION['logged_user']->type == 'admin' or $_SESSION['logged_user']->type == 'super_user') : //Если активный имеет тип учетной записи admin
        ?>
            <?php
    if($_SESSION['sign_up'] == 'true') { //Если есть ячейка об успешной регистрации пользователя
        echo "<br><div class='ok'>Вы успешно зарегестрировали нового пользователя!</div>";  // Выводим сообщение об успешной регистрации
        unset($_SESSION['sign_up']); //Удаляем ячейку
    } 
            ?>
            <h2>Таблица всех пользователей</h2>
            <table>
                <tr>
                    <td>Тип учетной записи</td>
                    <td>Фамилия</td>
                    <td>Имя</td>
                    <td>Отчество</td>
                    <td>Дата рождения</td>
                    <td>Номер телефона</td>

                <?php
    $allusers = R::findAll('users', 'ORDER BY type ASC '); //Поиск всех записей и сортировка их по типу учетной записи 
    foreach($allusers as $auser) { //Прокручиваем построчно массив который содержит данные о всех пользователях
        if ($auser['type'] == 'super_user') { //При выводе владельца убираем кнопку удалить
            echo "<tr><td>".$auser['type']."</td><td>".$auser['first_name']."</td><td>".$auser['name']."</td><td>".$auser['patronymic']."</td><td>none</td><td>none</td><td>Владелец</td></tr>"; //Выводим данные в таблицу
        }
        else {
            echo "<tr><td>".$auser['type']."</td><td>".$auser['first_name']."</td><td>".$auser['name']."</td><td>".$auser['patronymic']."</td><td>".$auser['year']."</td><td>".$auser['phone']."</td><td>"; //Выводим данные в таблицу
    
            echo "<form action='index.php' method='POST'><input type='hidden' name='id_user' value='".$auser['id']."'><button type='submit' name='delete_user'> Удалить</button></form></td></tr>";
        }
    }  
    echo "</table>";
                ?>

            </table>
            <br>
            <a href='signup.php'>Добавить пользователя</a><br>

            <?php
    elseif ($_SESSION['logged_user']->type == 'user') : //Если активный пользователь пимеет тип учетной записи user
            ?>

            <h3>Ваши записи</h3>
                    
            <?php   
    echo "<table>
        <tr>
            <td>ФИО сотрудника</td>
            <td>Дата</td>
            <td>Время</td>
            <td></td>
        </tr><tr>";
        foreach ($appointment_list as $aappointment_list) { //Выводим построчно масив содержащий все записи на приём
            if ($aappointment_list['user_id'] == $_SESSION['logged_user']->id) {  //Выводим записи только если они принадлежат активному пользователю 
                $employee_list = R::findLike('users',['id' => [$aappointment_list['employee']]],'ORDER BY first_name ASC'); //Подгружаем данные из базы пользователей по сотруднику который указан в записи
                foreach ($employee_list as $aemployee_list) { //Построчно выводим массив с данными сотрудника (хоть и сдесь всегда будет одна строка)
                    echo "<td>".$aemployee_list['first_name']." ".$aemployee_list['name']." ".$aemployee_list['patronymic']."</td>"; //Выводим ФИО сотрудника
                }        
                echo "<td>".$aappointment_list['date']."</td><td>".$aappointment_list['time']."</td><td>"; //Выводим дату и время записи
                echo "<form action='index.php' method='POST'><input type='hidden' name='id_appointment' value='".$aappointment_list['id']."'><button type='submit' name='delete_appointment'> Удалить</button></form></td></tr>"; //Добавляем форму которая передает id конкретной записи для ее удаления
                $i=0; //Добавляем счетчик циклов
                $i++; //При каждом цикле +1 к переменной (Инкремент)
            }
        }    
        if ($i==0) { //Если счетчик равен нулю 
               echo "<tr><td colspan='3'>Вы еще не записывались к специалисту</td></tr></table>"; //выводим сообщение о том что записей нет
            } 
        else {   
        echo "</table>";
        }
            ?>
                    
            <h3>Записаться на консультацию.</h3>
                    
            <?php  
    if ($_SESSION['appointment'] == 'true') { //Если есть ячейка об успешной записи на приём
        echo "<p>Вы успешно записались на прием!</p>"; //Выводим сообщение об успешной записи
        unset($_SESSION['appointment'],$data['employee'],$data['date'],$data['time']); //Удаляем ячейку
    }
            ?>
                    
            <h4>Выбирите бухгалтера</h4>
            <form action="index.php" method="POST">
                <?php
    if (isset($errors['appointment'])) { //Если ячейка appointment масива errors не пуста исполняеться тело IF цикла
        echo " <div class='errors'>".$errors['appointment']."</div>"; //Выводим ошибку
    }
    $search = 'employee'; //Создаем переменную и присваеваем ей значение employee что бы использовать ее как условие отбора записей из базы данных
    $employee = R::findLike('users',['type' => [$search]],'ORDER BY first_name ASC'); //Подгружаем данные из таблицы Users данные где ячейка type ровна заранее добавленному условию
    foreach($employee as $aemployee) { //Построчно прокручиваем массив содержащий данные о сотрудниках 
        echo "<input type='radio' name='employee' value='".$aemployee['id']."'"; //Выводим сотрудников для последубщего выбора пользователем
        if ($data['employee'] == $aemployee['id']) { //Если была ошибка записи на приём то будет выбран раннее выбранный сотрудник
            echo "checked"; //выводим параметр "выбрынный" в html код
        }
        echo ">".$aemployee['first_name']." ".$aemployee['name']." ".$aemployee['patronymic']."<br>"; //Выводим ФИО сотрудника
    }
    if (isset($errors['employee'])) { //Если ячейка eployee масива errors не пуста исполняеться тело IF цикла
        echo "<div class='errors'>".$errors['employee']."</div>"; //Выводим ошибку
    }
    $d1=date("d")+1; //Создаем переменную и присваеваем ей значение сегоднешнего дня +1 для что бы можно было записаться начиная с завтрешнего дня
    $d2=date("d")+14; //Создаем переменную и присваеваем ей значение сегодняшнего дня +14 что бы можно было записаться только на 2 недели вперед
                ?>
                <input type="date" min="<?php echo date('Y-m-').$d1;?>" max="<?php echo date('Y-m-').$d2;?>" name="date" value="<?php echo @$data['date'] ?>">  <!--  Добавляем ограничение на дату, а так же если была ошибка записи на приём, то добавляем ранее выбранную дату -->
                       
                <?php 
    if (isset($errors['date'])) { //Если ячейка eployee масива errors не пуста исполняеться тело IF цикл
        echo "<br><br><div class='errors'>".$errors['date']."</div>"; //Выводим ошибку
    }
    else echo "<br><br>"; //Добавляем перенос строки
                ?>

                <select size="1" name="time">
                    <option <?php   if (empty($data['time'])) {
                                        echo "selected"; //Если при ошибке записи на приём не была выбранно время то будет выбронно значение по умолчанию
                                    } ?> value="">Выберите время</option>
                    <option <?php   if ($data['time'] == '9:00') {
                                        echo "selected"; //При ошибке записи на приём будет выбранно ранее выбранное время
                                    } ?> value="9:00">с 9:00 до 10:00</option>
                    <option <?php   if ($data['time'] == '10:00') {
                                        echo "selected";
                                    } ?>  value="10:00">с 10:00 до 11:00</option>
                    <option <?php   if ($data['time'] == '11:00') {
                                        echo "selected";
                                    } ?>  value="11:00">с 11:00до 12:00</option>
                    <option <?php   if ($data['time'] == '13:00') {
                                        echo "selected";
                                    } ?>  value="13:00">с 13:00 до 14:00</option>
                    <option <?php   if ($data['time'] == '14:00') {
                                        echo "selected";
                                    } ?>  value="14:00">с 14:00 до 15:00</option>
                    <option <?php   if ($data['time'] == '15:00') {
                                        echo "selected";
                                    } ?>  value="15:00">с 15:00 до 16:00</option>
                    <option <?php   if ($data['time'] == '16:00') {
                                        echo "selected";
                                    } ?>  value="16:00">с 16:00 до 17:00</option>
                    <option <?php   if ($data['time'] == '17:00') {
                                        echo "selected";
                                    } ?>  value="17:00">с 17:00 до 18:00</option>
                </select>
                        
                <?php 
    if (isset($errors['time'])) { //Если ячейка eployee масива errors не пуста исполняеться тело IF цикл
        echo "<br><br><div class='errors'>".$errors['time']."</div>"; //Выводим ошибку
    }
    else echo "<br><br>";
                ?>
                <button type="submit" name="do_appointment">Записаться</button>
            </form>
                    
                <?php
    elseif ($_SESSION['logged_user']->type == 'employee') : //Если активный пользователь имеет тип учетной записи employee
                ?>
                <h2>Записи к вам</h2>
            
                <?php 
    echo "<table>
        <tr>
            <td>ФИО клиента</td>
            <td>Телефон</td>
            <td>Дата</td>
            <td>Время</td>
        </tr><tr>";
            foreach ($appointment_list as $aappointment_list) { //Постросно выводим список записей
                if ($aappointment_list['employee']==$_SESSION['logged_user']->id) {  //Выводим только записи к активному пользователю
                    $user_list = R::findLike('users',['id' => [$aappointment_list['user_id']]],'ORDER BY first_name ASC'); //Подгружаем данные пользователя из конкретной записи
                    foreach ($user_list as $auser_list) { //Построчно выводим массив с данными пользователя
                        echo "<td>".$auser_list['first_name']." ".$auser_list['name']." ".$auser_list['patronymic']."</td><td>".$auser_list['phone']."</td>"; //Выводим ФИО пользователя
                    }
                    echo "<td>".$aappointment_list['date']."</td><td>".$aappointment_list['time']."</td><td>"; //Выводим дату и время записи
                    echo "<form action='index.php' method='POST'><input type='hidden' name='id_appointment' value='".$aappointment_list['id']."'><button type='submit' name='delete_appointment'> Удалить</button></form></td></tr>"; //Добавляем форму которая передает id конкретной записи для ее удаления
                $i=0; //Добавляем счетчик циклов
                $i++; //При каждом цикле +1 к переменной (Инкремент)
            }
        }    
        if ($i==0) { //Если счетчик равен нулю 
               echo "<tr><td colspan='3'>Записей к вам на прием нет</td></tr></table>"; //Выводим сообщение о том что записей нет
            } 
        else {   
        echo "</table>";
        }
            endif //Конец цикла определяющего тип учетной записи активного пользователя
            ?>

            </div>
            </section>
</body>
</html>
