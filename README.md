
Архитектура фреймворка GENERATION II (G2).
=====================
```
├── app                                # Main file structure directory
│   ├── subproject1                    # Directory Subproject1 
│   │   └── model                      # Folder model
│   │       └── Model1.php             # Model1 work to Database
│   │       └── Model2.php             # Model2 work to Database
│   │       └── ...                
│   │   └── PublicNameController.php   # Access without security_key
│   │   └── PrivateNameController.php  # Access with security_key
│   ├── subproject2                    # Name Subproject2
│   └── ...           
│   
├── core                               # engine directory G2
├── frontend ***                       # Folder frontend to in progress
│   ├── assets ***               
│   ├── stylesheets ***              
│   └── vendor ***                    
├── index.php                          # Endpoint url
└── .htaccess                          # htaccess rewriting all of requests to endpoint /index.php
```


Структура микросервиса 
=====================

Микросервис (подпроект) - это каталог, который состоит из каталога model, PublicExampleController.php, PrivateExampleController.php .

Каталог микросервиса хранится в корне каталога /app/ .

Каждый микросервис отвечает за работу небольших компонентов системы, такие как авторизация и регистрация пользователей, работа с заявками, уведомления, новости и так далее. 

Каталог model хранит в себе множество моделей (сущностей), которые работают с БД и выводят результат в виде JSON.


### PublicExampleController.php

Это публичный контроллер который не требует наличия security_key.


### PrivateExampleController.php


Это приватный контроллер который требует наличие security_key.

security_key - это сгенерированный ключ, который записывается в cookie браузера после авторизации пользователя.



BACKEND API G2
=====================

API предоставляется по ссылке http://site.ru/api/name_microservice/name_method?param1=1&param2=2

Пример

` https://g2.qzo.su/api/auth/getExample?id=1 `

auth - название контроллера **Auth**Controller (/app/**auth**/**Auth**Controller.php)
getExample - метод, которые вызывается в контроллере **Auth**Controller

```
class AuthController extends Controller{

	public function example(){ echo (new Authorization())->example(); }

	...
}

```

** Формат возвращаемых данных в формате JSON **

Функции и возможности Model
=====================

### Работа с GET и POST параметрами

В Model существует массив $params_url, которые хранит в себе все переданные данные в запросе.

Обратится к нему можно так:
` self::$params_url['id'] `,
где id - это ключевое имя параметра в запросе.


### Отправка даных в формат JSON 

Функция $this->viewJSON($data = null, $error = '', $type = null)
	$data - данные, которые необходимо преобразовать в JSON,
	$error - переменная, содержащая в себе данные об ошибке
	$type - (по умолчанию равен null), 
			если $type = "mobile", то данные вернутся с callback
			если $type = null, то данные вернутся без callback

```
$this->viewJSON($data);
$this->viewJSON(array('error' => array("text" => "Text error", "code" => 1010))); - пример вывода ошибок
$this->viewJSON($data, "mobile");

```

Работа с БД
=====================

### 1.Выборка 

```
Model::table("x16_table")->get(array("id", "login"))->filter(array("id" => 1))->sort("id", "desc")->pagination(0,6)->send()

->get(array("field1", "field2")) - список полей, которые получаем из таблицы
->get() - получаем все поля из таблицы

->filter(array("field1" => 1, "field2" => "example")) -  сравниваем поля 

->sort("id", "desc") - сортировка поля id по DESC 
->sort("id", "asc") - сортировка поля id по ASC

->pagination(0,6) - указываем LIMIT 0, 6

->send() - отправка запроса 

```


### 2.Добавление новых записей 

```
Model::table("x16_table")->add(array("login" => "example@gmail.com", "pass" => "dfvkldfmlcdkfv"))->send(); 

->add(array("login" => "example@gmail.com", "pass" => "dfvkldfmlcdkfv")) - заполняем поле таблицы login и pass данными

->send() - отправка запроса

```

### 3.Редактирование записей 

```
Model::table("x16_table")->edit(array("field1" => $field1, "field2" => $field2), array("id" => 1))->send()

->edit(array("field1" => $field1, "field2" => $field2) - заполняем поля таблицы данными, которые хотим отредактировать

->send() - отправка запроса

```

### 4.Удаление 

` Model::table("x16_table")->delete(array("id" => 4))->send(); `

### 5.Собственный запрос PDO

```
$stmt = self::$db->prepare("SELECT * FROM  `x16_table` WHERE id= :id AND status = :status");

$result_query = $stmt->execute(array(":id" => self::$params_url['id'], ":status" => self::$params_url['status']));

$rows = $stmt->fetchAll(PDO::FETCH_ASSOC); 

$this->viewJSON($rows);

// где self::$params_url['id'] - это GET или POST параметр, переданный в запросе.
```
### 5. Метод Model::getQuery()

Получение отправляемого запроса в БД

```
SELECT id FROM x16_users WHERE login= :filter_login AND password= :filter_password
```



Работа с фронтендом
=====================

### Структура каталога frontend

...

### Работа со страницами

Пример URL страницы https://site.ru/page

page - название страницы, она находится по /app/frontend/pages/page.html


Панель администрирования проектов
=====================

> Здесь ведутся технические работы с блэк джеком и кодом



