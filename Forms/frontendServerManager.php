<?php
/**
 * Created by PhpStorm.
 * User: hreeves
 * Date: 9/12/2018
 * Time: 2:03 PM
 */
include '../../TonyAmos/Classes/DBHelper.php';
include '../../TonyAmos/Classes/TonyDBHelper.php';
$DB = new TonyDBHelper();
$dbName = "bchobs1";

// Getting drop down list for year
if(isset($_POST["collection"]))
{
    $dbName = $_POST["collection"];
}

else
{
    echo "Something went wrong in frontendServerManager.php.";
}