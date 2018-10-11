<?php
/**
 * Created by PhpStorm.
 * User: hreeves
 * Date: 8/15/2018
 * Time: 12:10 PM
 */
include '../../TonyAmos/Classes/DBHelper.php';
include '../../TonyAmos/Classes/TonyDBHelper.php';
$DB = new TonyDBHelper();
$dbName = "tonyamosdb";

// Checking to see if the parameters are set
if(isset($_POST["ids"]) && isset($_POST["birdCode"]) && isset($_POST["birdCodeDescription"]))
{
    $ids = json_decode($_POST["ids"]);
    $birdCode = json_decode($_POST["birdCode"]);
    $birdCodeDescription = json_decode($_POST["birdCodeDescription"]);
    $tableName = "birdcodes";
    $counter = 0;

    foreach($ids as $id)
    {
        if($DB->INSERT_INTO_BIRD_CODES($id, $birdCode[$counter], $birdCodeDescription[$counter], $dbName, $tableName))
        {
            $counter++;
            continue;
        }

        else
        {
            break;
        }
    }
}

else if(isset($_POST["year"]) && !isset($_POST["insertRecordDay"]))
{
    $DB->GET_YEAR_FILE($_POST["year"]);
    $DB->GET_MILE_MARKER_FILE_FROM_YEAR($_POST["year"]);

    $arrayOfMileMarkers = $DB->getArrayDaysMileMarker();

    echo json_encode($arrayOfMileMarkers);
}

else if(isset($_POST["getBirdCodes"]))
{
    if($DB->GET_ALL_BIRD_CODES($dbName))
    {
        $arrayOfBirdCodes = $DB->getArrayOfBirdCodes();
        echo json_encode($arrayOfBirdCodes);
    }

    else
    {
        echo "Something went wrong with selecting the bird codes.";
    }
}

else if(isset($_POST["insertObserved"]) && isset($_POST["firstTime"]))
{
    // A boolean value that lets me know that this is the first call
    $firstTime = $_POST["firstTime"];

    // Year one in yearday is January 1 1978
    $arrayOfBirdCodes = json_decode($_POST["insertObserved"]);
    date_default_timezone_set('America/Chicago');

    if(empty($arrayOfBirdCodes))
    {
        echo "Empty";
    }

    else
    {
        $counter = 0;
        $leapYear = false;
        $dup = 0;

        // Getting year and day from file name
        preg_match("/\\/\\d{3}/",  $arrayOfBirdCodes[0]->filename, $day);
        $day = implode("", $day);
        $day = substr($day, 1);
        $tempDay = $day;

        preg_match("/\\d\\d\\d\\d/",  $arrayOfBirdCodes[0]->filename, $year);
        $year = implode("", $year);

        // Checking to see if leap year
        if(((int) $year % 4) == 0 && ((int) $year % 100) != 0 || ((int) $year %400) == 0)
        {
            $leapYear = true;
        }

        preg_match("/M\\d*/", $arrayOfBirdCodes[0]->filename, $milemarker);
        $milemarker = implode("", $milemarker);
        $milemarker = substr($milemarker, 1);

        $tempMilemarker = $milemarker;

        // Looping through object
        foreach($arrayOfBirdCodes as $value)
        {
            $insert = true;

            // Getting the day (ex 001) and milemarker (ex 8)
            preg_match("/\\/\\d{3}/",  $value->filename, $day);
            $day = implode("", $day);
            $day = substr($day, 1);

            preg_match("/M\\d*/", $value->filename, $milemarker);
            $milemarker = implode("", $milemarker);
            $milemarker = substr($milemarker, 1);

            // Checking if dup
            if(strpos($value->filename, 'DUP') !== false)
            {
                $dup = 1;
            }

            else
            {
                $dup = 0;
            }

            if($leapYear == true && (int) $day >= 59)
            {
                // Setting the date and time, but extracting just the date. Ex.) 11-12-1985
                $datetime = DateTime::createFromFormat('z Y', strval((int) $day - 2) . ' ' . strval((int) $year));
                preg_match("/\\d*-\\d*-\\d*/", date_format($datetime, 'm-d-Y H:i:s'), $date);
                $date = implode("", $date);
                /*echo $date . "///" . $day . "<br>";*/

                if($datetime !== false)
                {
                    preg_match("/\\d*-\\d*-\\d*/", date_format($datetime, 'm-d-Y H:i:s'), $date);
                    $date = implode("", $date);

                    // Getting julian time
                    $juliantime = gregoriantojd((int) date_format($datetime, "m"), (int) date_format($datetime, "d"), (int) date_format($datetime, "Y"));
                }

                else
                {
                    $date = "00/00/0000";

                    // Getting julian time
                    $juliantime = 0;
                }
            }

            else
            {
                // Setting the date and time, but extracting just the date. Ex.) 11-12-1985
                $datetime = DateTime::createFromFormat('z Y', strval((int) $day - 1) . ' ' . strval((int) $year));

                if($datetime !== false)
                {
                    preg_match("/\\d*-\\d*-\\d*/", date_format($datetime, 'm-d-Y H:i:s'), $date);
                    $date = implode("", $date);

                    // Getting julian time
                    $juliantime = gregoriantojd((int) date_format($datetime, "m"), (int) date_format($datetime, "d"), (int) date_format($datetime, "Y"));
                }

                else
                {
                    echo "$day      $year<br>";
                    $date = "00/00/0000";

                    // Getting julian time
                    $juliantime = 0;
                }
            }

            // First time call to TonyQuery.php
            if ($firstTime === "true" && $insert)
            {
                $firstTime = "false";
                $tempMilemarker = $milemarker;
                $DB->INSERT_INTO_BC_DATE($day, $date, $juliantime, (int)$milemarker, $dbName);
                $insert = false;
            }

            // Checking to see if mile markers have changed
            if ($tempDay !== $day)
            {
                $tempDay = $day;
                $DB->INSERT_INTO_BC_DATE($day, $date, $juliantime, (int)$milemarker, $dbName);
            }

            if ((int)$value->distance >= 0)
            {
                $DB->INSERT_INTO_BC_OBSERVATIONS($value->birdcode, (int)$value->amount, $date, $juliantime, (int)$value->distance, (int)$milemarker, $day, $dup, $dbName);
            }

            else
            {
                $DB->INSERT_INTO_BC_OBSERVATIONS($value->birdcode, (int)$value->amount, $date, $juliantime, null, (int)$milemarker, $day, $dup, $dbName);
            }
        }
    }
}

else if(isset($_POST["getDDL"]))
{
    $DB->GET_DDL_YEARS($dbName, $_POST["getDDL"]);
}

// For populating the days table in tonamos.php for csv generation
else if(isset($_POST["ddlYearValue"]) && isset($_POST["notdelete"]))
{
    $DB->GET_DDL_DAYS($dbName, $_POST["ddlYearValue"], false);
}

// For populating the days table in tonyamos.php for deletion from db
else if(isset($_POST["ddlYearValue"]))
{
    $DB->GET_DDL_DAYS($dbName, $_POST["ddlYearValue"], true);
}

else if(isset($_POST["deleteRecordDay"]))
{
    $DB->DELETE_ALL_OBSERVATIONS_BY_DAY($dbName, $_POST["deleteRecordDay"]); // Delete day from bc_observations table
    $DB->DELETE_DAY($dbName, $_POST["deleteRecordDay"]); // Delete day from bc_date table
}

else if(isset($_POST["getFilesFromYear"]))
{
    $DB->GET_DDL_FILES_BY_YEAR($_POST["getFilesFromYear"]);
}

else if(isset($_POST["getBirdIds"]))
{
    if($DB->GET_ALL_BIRD_IDS($dbName))
    {
        $arrayOfBirdIds = $DB->getArrayOfBirdIds();
        echo json_encode($arrayOfBirdIds);
    }

    else
    {
        echo "Something went wrong with selecting the bird codes.";
    }
}

// To insert a single file to DB
else if(isset($_POST["insertRecordDay"]) && $_POST["year"])
{
    /*echo $_POST["insertRecordDay"];*/
    // arrayDaysMileMarker
    /*echo $_POST["year"];*/
    $DB->GET_MILE_MARKER_FILE_FROM_DAY($_POST["year"], $_POST["insertRecordDay"]);

    echo json_encode($DB->getArrayDaysMileMarker());

}

else if(isset($_POST["checkDBForMissingRecords"]))
{
    // An array that looks like this if var dumping:
    // [0]=>
    // array(2) {
    // ["bc_date"]=>
    // string(10) "01-01-1985"
    // ["bc_yearday"]=>
    // string(3) "001"

    $dbResult = $DB->CHECK_DB($dbName);
    $DB->GET_ALL_FILE_NAMES(); // Setting allFiles
    $allFiles = $DB->getAllFiles(); // Getting allFiles

    // Initialize array
    $dbResultFiltered = [];
    $counter = 0;

    foreach($dbResult as $element)
    {
        // Getting year as a string
        preg_match('/[0-9]{4}/', $element["bc_date"], $year);
        $year = implode("", $year);

        // Getting day as a string
        preg_match('/[0-9]{3}/', $element["bc_yearday"], $day);
        $day = implode("", $day);

        // Saving $dbResultFiltered into the same format as $allFiles
        $dbResultFiltered[$counter] = $year . "/" . $day;
        $counter++; // Increment
    }

    $result = array_diff($allFiles, $dbResultFiltered);
    var_dump($result);
}

// Getting all the primary keys of a day from a specified year
else if(isset($_POST["GET_PRIMARYKEYS_DAYS"]) && isset($_POST["yearCSV"]))
{
    $data = $DB->GET_PRIMARYKEYS_OF_DAY_FROM_YEAR($dbName, $_POST["yearCSV"]);
    echo json_encode($data);
}

else if(isset($_GET["selectAll"]))
{
    $data = $DB->POPULATE_DATA_TABLE($dbName, $_GET["selectAll"]);

    echo json_encode(array('data' => $data));
}

else if(isset($_GET["getDay"]) && isset($_GET["year"]))
{
    $data = $DB->POPULATE_DATA_TABLE_DAY($dbName, $_GET["getDay"], $_GET["year"]);

    echo json_encode(array('data' => $data));
}

// Updating bc_date_object
else if(isset($_POST["bc_date_object"]))
{
    // Getting bc_date as an array
    $dateArray = $DB->GET_BC_DATE($dbName);
    $update = array();

    foreach($dateArray as $key => $value)
    {
        $result = "";

        // Assigning temporary variable
        $date = $value;

        $array = explode("-", $date);

        // Element 0: month, Element 1: day, Element 2: year
        //$result = $array[1] . "-" . $array[0] . "-" . $array[2];
        $result = $array[2] . "-" . $array[0] . "-" . $array[1];
        $newdate = date('Y-m-d', strtotime($result));

        $DB->UPDATE_BC_DATE($dbName, $newdate, $key);

        $result = "";
    }
}
?>