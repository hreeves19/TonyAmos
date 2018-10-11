<?php
/**
 * Created by PhpStorm.
 * User: hreeves
 * Date: 9/28/2018
 * Time: 8:25 AM
 */

include '../../TonyAmos/Classes/DBHelper.php';
include '../../TonyAmos/Classes/TonyDBHelper.php';

$dbName = "tonyamosdb";
$queryHelper = new TonyDBHelper();

if(isset($_GET["query"]))
{
    $data = $queryHelper->POPULATE_DATA_TABLE_WHERE($dbName, $_GET["query"]);

    echo json_encode(array('data' => $data));
}

else if(isset($_POST["year"]) && $_POST["ddlMonth"])
{
    $queryHelper->GET_DDL_MONTH($dbName, $_POST["year"]);
}

else
{
    echo "Data passed is unrecognizable in processQuery.php";
}
?>