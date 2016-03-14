<?class DB {

    protected static $_instance;  //экземпляр объекта

    public static function run() { // получить экземпляр класса PDO
        if (self::$_instance === null) { // если экземпляр  класса  не создан
            try {
                self::$_instance = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME, DB_USER, DB_PASS,array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
                self::$_instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                throw new Exception('Ошибка соединения с базой данных '.$e->getMessage()); //вывод ошибок
            }
        }
        else{
            echo "Подключение к Базе данных ".DB_NAME. " было выполнено."; // вывод ошибки при повторном создании экземпляра
        }
        return self::$_instance; // возвращаем экземпляр данного класса
    }


    private  function __construct() {
    }

    private function __clone() { //запрещаем клонирование объекта модификатором private
    }

    private function __wakeup() {}
    //метод выполнения скрипта.

    public static function query($sql) {

        $obj=self::$_instance;

        if(isset($obj)){
            try{
            $obj->count_sql++;
            $start_time_sql = microtime(true);
            $result=$obj->query($sql)or die("<br/><span style='color:red'>Ошибка в SQL запросе:</span> ");
            $time_sql = microtime(true) - $start_time_sql;

                //вывод количество запросов и время на выполнение
//            echo "<br/><br/><span style='color:blue'> <span style='color:green'># Запрос номер ".$obj->count_sql.": </span>".$sql."</span> <span style='color:green'>(".round($time_sql,4)." msec )</span>";

            return $result;
            }
            catch (PDOException $e){
                echo($e->getMessage());
            }
        }
        return false;
    }

    //возвращает запись в виде ассоциативного массива
    public static function fetchAll($object)
    {
        return $object->fetchAll(PDO::FETCH_ASSOC);
    }


    //создает утверждение по шаблону запроса, в который будут подставлены параметры
    public static function prepare($sql){
        return DB::$_instance->prepare($sql);
    }



}