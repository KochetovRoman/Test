<!DOCTYPE html>
<html>
<head>
<title>Тестовое задание</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Bootstrap -->
<link href="css/bootstrap.min.css" rel="stylesheet" media="screen">
</head>
<body>

<?php

final class myTest {

    protected $db;
    private $name = 'testdb';
    private $host = 'localhost';
    private $user = 'root';
    private $pass = '';

       /**
     * Подключение к базе данных
     * Подключение к базе данных с использованием PDO
     * @db экземпляр класса pdo
     * @return bool
     */

    private function connect() {

        try {

            $dsn = 'mysql:dbname='.$this->name.';host='.$this->host;
            $db = new PDO($dsn, $this->user, $this->pass);
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        } catch (Exception $e) {

            echo $e->getMessage();
            return false;
        }

        return $db;
    }

       /**
     * Конструктор
     */

    public function __construct() {

        $this->db = $this->connect();
        $this->create();
        for ($i=0; $i < 500; $i++) {

            $this->fill();
        }
    }

       /**
     * Создание таблицы
     * @table строковый sql запрос
     * @exec возвращает количество строк, которые были изменены или удалены в ходе его реализации
     */

    private function create() {

        $table = "CREATE TABLE IF NOT EXISTS `myTable` ( 
                `id` INT NOT NULL AUTO_INCREMENT, 
                `script_name` VARCHAR(25) NOT NULL , 
                `script_execution_time` DECIMAL(4,2) NOT NULL ,  
                `script_result` ENUM('active','failed','success') NOT NULL,
                PRIMARY KEY (`id`)
                ) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_unicode_ci;";
        $tab = $this->db->exec($table);

        return true;
    }

       /**
     * Рандомная генерация строки
     * @param int $length
     * @return строка
     */

    public function randomString($length = 10) {
        $str = "";
        $characters = array_merge(range('A','Z'), range('a','z'), range('0','9'));
        $max = count($characters) - 1;
        for ($i = 0; $i < $length; $i++) {
            $rand = mt_rand(0, $max);
            $str .= $characters[$rand];
        }

        return $str;
    }

       /**
     * Рандомный элемент массива
     * @param массив $enum
     * @return строка
     */

    public function randomArr(array $enum) {

        $count = count($enum) - 1;
        $rand  = mt_rand(0, $count);
        return $enum[$rand];
    }


       /**
     * Вставка случайных данных
     * @sql строковый sql запрос
     * @stmt результат запроса
     * @prepare возвращает PDOStatement object
     * @bindParam связывает параметр с указанным именем переменной
     * @execute выполняет запрос
     */

    private function fill() {

        $sql = "INSERT INTO `myTable` (script_name, script_execution_time, script_result) 
                VALUES (:script_name, :script_execution_time, :script_result)";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':script_name', $this->randomString(), PDO::PARAM_STR);
        $stmt->bindParam(':script_execution_time', time(), PDO::PARAM_INT);
        $stmt->bindParam(':script_result', $this->randomArr(['active', 'failed', 'success']), PDO::PARAM_STR);
        $execute = $stmt->execute();

        if(!$execute) throw new Exception("SQL запрос не выполняется!");

    	return true;
    }

       /**
     * Получаем данные из таблицы
     * @prepare возвращает PDOStatement object
     * @stmt результат запроса
     * @execute выполняет запрос
     * @row выбирает следующую строку из набора результатов
     */

    public function get() {

            $stmt = $this->db->prepare("SELECT id, script_name, script_execution_time, script_result FROM `myTable` WHERE script_result IN (?, ?)");
            $stmt->execute(['failed', '']);

            echo "<table class='table table-hover'><thead><tr><th>".'id'."</th><th>".'script_name'."</th><th>".'script_execution_time'."</th><th>".'script_result'."</th></tr></thead><tbody>";

            while ($row = $stmt->fetch(PDO::FETCH_LAZY)) {

                echo "<tr><td>".$row['id']."</td><td>".$row['script_name']."</td><td>".$row['script_execution_time']."</td><td>".$row['script_result']."</td></tr>";                

            }

            echo "</tbody></table>";
    }

    
}

$test = new myTest();
$test->get();

?>

<script src="http://code.jquery.com/jquery.js"></script>
<script src="js/bootstrap.min.js"></script>
</body>
</html>