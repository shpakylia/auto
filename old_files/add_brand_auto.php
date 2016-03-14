<html>
<head>
    <meta charset="UTF-8"/>
    <link rel="stylesheet" href="style.css"/>
</head>
<body>
<h3> Добавить бренд автомобиля</h3>
<table id="brand">
<form method="post" action="#">
    <tr>
        <td> <span>Бренд:</span></td>
        <td><input type="text" name="brand_name" placeholder="Введи бренд"/></td>
    </tr>
    <tr>
        <td><span>Сайт производителя:</span></td>
        <td><input type="text" name="generator_url" placeholder="url сайта"/></td>
    </tr>
    <tr> <td colspan="2" class="submit"><input type="submit" name="submit"/></td></tr>
    
</form>
</table>
<?php
include_once ('class.db.php');
define('DB_HOST', 'localhost');//сервер
define('DB_USER', 'root');// пользователь
define('DB_PASS', '');//пароль
define('DB_NAME', 'sto_auto');// имя базы

DB::run();

if($_POST["submit"]){
    if($_POST['brand_name'] ){
        $array_param['brand_name'] = $_POST['brand_name'];
    }
    else{
        $array_param['brand_name'] = NULL;
    }
    if($_POST['generator_url']){
        $array_param['generator_url'] = $_POST['generator_url'];
    }
    else{
        $array_param['generator_url'] = NULL;
    }



    echo('<pre>');
//    var_dump($array_param);

    $sql = "insert into brands (brand_name, generator_url) VALUES (:brand_name, :generator_url)";
    //$result = DB::query($sql);
    $result = DB::prepare($sql);
    $result->execute($array_param);
}
    $sql_show_brand = "select brand_name, generator_url from  brands order by id DESC limit 7";
    $show_result = DB::query($sql_show_brand);
    $show_brand = DB::fetchAll($show_result);
//    $show_brand = array_reverse ($show_brand);
//    var_dump($show_brand);


    //$brands = DB::fetchAll($result);
    //echo("<pre>");
    //var_dump($brands);

?>
<table class="show_modal">
    <caption>Добавленные данные:</caption>
    <tr>
        <th>Бренд</th>
        <th>Сайт производителя</th>
    </tr>

    <?php
    foreach ($show_brand as $val){
        echo "<tr><td>".$val['brand_name']."</td>";
        echo "<td>".$val['generator_url']."</td></tr>";
    }
    ?>
</table>
</body>
</html>
