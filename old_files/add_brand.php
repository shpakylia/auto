
<?php

if($_POST){
    if(isset($_POST['brand_name'])){
        $array_param['brand_name'] = $_POST['brand_name'];
    }
    else{
        $array_param['brand_name'] =NULL;
    }
    if(isset($_POST['generator_url'])){
        $array_param['generator_url'] = $_POST['generator_url'];
    }
    else{
        $array_param['generator_url'] =NULL;
    }


}
echo('<pre>');
var_dump($array_param);

include_once ('class.db.php');
define('DB_HOST', 'localhost');//сервер
define('DB_USER', 'root');// пользователь
define('DB_PASS', '');//пароль
define('DB_NAME', 'sto_auto');// имя базы

DB::run();
$sql = "insert into brands (brand_name, generator_url) VALUES (:brand_name, :generator_url)";
//$result = DB::query($sql);
$result = DB::prepare($sql);
$result->execute($array_param);
//$brands = DB::fetchAll($result);
//echo("<pre>");
//var_dump($brands);
?>