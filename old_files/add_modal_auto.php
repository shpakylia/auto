<html>
<head>
    <meta charset="UTF-8"/>
    <link rel="stylesheet" href="style.css"/>
</head>
<body>

<?php
include_once ('class.db.php');
define('DB_HOST', 'localhost');//сервер
define('DB_USER', 'root');// пользователь
define('DB_PASS', '');//пароль
define('DB_NAME', 'sto_auto');// имя базы

DB::run();
$sql_brand = "select brand_name from brands";
$obj_brands = DB::query($sql_brand);
$brands_list = DB::fetchAll($obj_brands);
?>
<h3> Добавить бренд автомобиля</h3>
<table id="brand">
<form method="post" action="#">
    <tr>
        <td>Выбери бренд:</td>
        <td>
            <select name="brand_name" id="select_brand">
                <?php
                foreach ($brands_list as $var){
                    echo "<option  value=".$var['brand_name'].">".$var['brand_name']."</option >";
                }
                ?>

            </select>
        </td>
    </tr>
    <tr>
        <td> <span>Модель:</span></td>
        <td><input type="text" name="modal_name" placeholder="Введи модель авто" required /></td>
    </tr>
    <tr>
        <td><span>Год выпуска:</span></td>
        <td><input type="text" name="years" placeholder="Введи года" required/></td>
    </tr>
    <tr>
        <td><span>Объем двигателя:</span></td>
        <td><input type="text" name="engine" placeholder="Введи объем двигателя" required/></td>
    </tr>
    <tr>
        <td><span>Привод:</span></td>
        <td><input type="text" name="gear" placeholder="Введи привод" required/></td>
    </tr>
    <tr>
        <td><span>АКПП:</span></td>
        <td><input type="text" name="type_akpp" placeholder="Введи АКПП" required/></td>
    </tr>
    <tr> <td colspan="2" class="submit"><input type="submit" name="submit"/></td></tr>
    
</form>
</table>
<?php

if($_POST["submit"]){
        $array_param['modal_name'] = $_POST['modal_name'];
        $array_param['years'] = $_POST['years'];
        $array_param['engine'] = $_POST['engine'];
        $array_param['gear'] = $_POST['gear'];
        $array_param['type_akpp'] = $_POST['type_akpp'];

        $ar_auto['brand_name'] = $_POST['brand_name'];
        $ar_auto['modal_name'] = $_POST['modal_name'];



    echo('<pre>');
//    var_dump($array_param);

    $sql = "insert into modals (modal_name,years,engine,gear,type_akpp) VALUES (:modal_name, :years,:engine,:gear,:type_akpp)";
    //$result = DB::query($sql);
    $result = DB::prepare($sql);
    $result->execute($array_param);

    $sql_auto = "insert into autos (id_brand,id_modal) VALUES ((select id from brands where brand_name = :brand_name), (select id from modals where modal_name = :modal_name))";
    $auto_result = DB::prepare($sql_auto);
    $auto_result->execute($ar_auto);

}
    $sql_show_auto = "select b.brand_name, m.modal_name, m.years, m.`engine`, m.gear, m.type_akpp from autos a join brands b on a.id_brand=b.id join modals m on a.id_modal = m.id order by a.id DESC limit 7";
    $auto_result = DB::query($sql_show_auto);
    $show_auto = DB::fetchAll($auto_result);
//    $show_brand = array_reverse ($show_brand);
//    var_dump($show_brand);


    //$brands = DB::fetchAll($result);
    //echo("<pre>");
    //var_dump($brands);

?>
<table class="show_brand">
    <caption>Добавленные данные:</caption>
    <tr>
        <th>Бренд</th>
        <th>Модель</th>
        <th>Год выпуска</th>
        <th>Объем двигателя</th>
        <th>Привод</th>
        <th>АКПП</th>
    </tr>

    <?php
    foreach ($show_auto as $v){
        echo "<tr>";
        echo"<td>".$v['brand_name']."</td>";
        echo "<td>".$v['modal_name']."</td>";
        echo "<td>".$v['years']."</td>";
        echo "<td>".$v['engine']."</td>";
        echo "<td>".$v['gear']."</td>";
        echo "<td>".$v['type_akpp']."</td>";
        echo"</tr>";
    }
    ?>
</table>
</body>
</html>
