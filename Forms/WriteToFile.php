<?php
/**
 * Created by PhpStorm.
 * User: hreeves
 * Date: 8/1/2018
 * Time: 2:30 PM
 */
include '../../TonyAmos/Classes/DBHelper.php';
include '../../TonyAmos/Classes/TonyDBHelper.php';
$DB = new TonyDBHelper();
$dbName = "tonyamosdb";

// Note: For CSV Generation, make sure the maximum execution time for a script is set high in the php.ini.
// I set mine to 1800 seconds, which is 30 minutes

// Checking to see if the array textByMileMarker (holds the string of each mile marker) has been sent from tonyAmosManager.js
// filename is a string of the file name (example: "048") and milemarkers is an array of integers (example: 0, 1, 2, 3....)
// error is an array of error messages
if(isset($_POST['arrayOfText']) && isset($_POST['filename']) && isset($_POST['arrayOfMileMarkers']) && isset($_POST['error']) && isset($_POST["collection"]))
{
    // Setting the default time zone
    date_default_timezone_set("America/Chicago");
    $date = date('Y-m-d H:i:s');
    $textByMileMarker = json_decode($_POST['arrayOfText']);
    $filename = $_POST['filename'];
    $mileMarkers = json_decode($_POST['arrayOfMileMarkers']);
    $arrayOfErrorMessage = json_decode($_POST['error']);
    $collection = $_POST["collection"];
    $counter = 0;
    $regexGetMileMarkers = "/\\* \\d/";
    $newArrayOfMileMarkers = [];

    // Make sure that you change these to the right year
    $year = "BDZ1984";
    $orginalYear = "BDZ84";



    // Making a new directory for the day of year
    mkdir("C:/xampp/htdocs/TonyAmos/Data/SortedData/$collection/$year/$filename");
    $originalFilePath = "C:/xampp/htdocs/TonyAmos/Data/Copy/$collection/BIRDRAW/$orginalYear/$filename";
    $originalfile = file_get_contents("C:/xampp/htdocs/TonyAmos/Data/OriginalData/$collection/BIRDRAW/$orginalYear/$filename");
    $originalfile = preg_replace('/\r?\n|\r/', "", $originalfile);

    // Removing text before mile marker 0, if there is one
    $position = strpos($originalfile, "* 0");
    if($position !== false)
    {
        $originalfile = substr($originalfile, $position);
    }

    // Creating a new text file for each mile marker
    foreach($textByMileMarker as $text)
    {
        // Overwriting original file
        $pos = strpos($originalfile, $text);

        if ($pos !== false)
        {
            $originalfile = substr_replace($originalfile,"",$pos,strlen($text));
        }

        // For files that may had no mile markers in it, but has keycodes
        else
        {
            $tempText = substr($text, 3);

            $pos = strpos($originalfile, $tempText);

            if ($pos !== false) {
                $originalfile = substr_replace($originalfile,"",$pos,strlen($tempText));
            }
        }

        // For duplicated mile markers
        if(array_search($mileMarkers[$counter], $newArrayOfMileMarkers) !== false)
        {
            // Writing mile marker to file
            file_put_contents("C:/xampp/htdocs/TonyAmos/Data/SortedData/$collection/$year/$filename/$counter" . "_M$mileMarkers[$counter]_DUP.txt", $text);
        }

        // For regular mile markers
        else
        {
            // Adding mile marker to an array, this will be used to check for duplicates
            file_put_contents("C:/xampp/htdocs/TonyAmos/Data/SortedData/$collection/$year/$filename/$counter" . "_M$mileMarkers[$counter].txt", $text);
            array_push($newArrayOfMileMarkers, $mileMarkers[$counter]);
        }

        // Checking to see if there is an extra mile marker in the text
        if(substr_count($text, "*") > 1)
        {
            // Retrieving negative mile markers in the text if they exists
            preg_match('/(\*\s*-[1-9]\d)|(\*\s*-\d)/', $text, $matches);

            // Checking to see if the first element of the array exists
            if(!empty($matches[0]))
            {
                // Duplicate Files
                if(file_exists("C:/xampp/htdocs/TonyAmos/Data/SortedData/$collection/$year/$filename/$counter" . "_M$mileMarkers[$counter]_DUP.txt"))
                {
                    array_push($arrayOfErrorMessage, "Negative mile marker $matches[0] found in $filename at mile marker $counter" . "_M$mileMarkers[$counter]_DUP. " .
                        "In C:/xampp/htdocs/TonyAmos/Data/SortedData/$collection/$year/$filename/$counter" . "_M$mileMarkers[$counter]_DUP.txt");
                }

                // Regular files
                else
                {
                    array_push($arrayOfErrorMessage, "Negative mile marker $matches[0] found in $filename at mile marker M$mileMarkers[$counter]. " .
                        "In C:/xampp/htdocs/TonyAmos/Data/SortedData/$collection/$year/$filename/$counter" . "_M$mileMarkers[$counter].txt");
                }
            }

            else
            {
                // Duplicate Files
                if(file_exists("C:/xampp/htdocs/TonyAmos/Data/SortedData/$collection/$year/$filename/$counter" . "_M$mileMarkers[$counter]_DUP.txt"))
                {
                    // var regexNegativeMileMarkers = /(\*\s*-\d\d)|(\*\s*-\d)|(\*)/gm;
                    array_push($arrayOfErrorMessage, "There is an invalid mile marker in $filename at mile marker $counter" . "_M$mileMarkers[$counter]_DUP. " .
                        "In C:/xampp/htdocs/TonyAmos/Data/SortedData/$collection/$year/$filename/$counter" . "_M$mileMarkers[$counter]_DUP.txt");
                }

                // Regular files
                else
                {
                    array_push($arrayOfErrorMessage, "There is an invalid mile marker in $filename at mile marker M$mileMarkers[$counter]. " .
                        "In C:/xampp/htdocs/TonyAmos/Data/SortedData/$collection/$year/$filename/$counter" . "_M$mileMarkers[$counter].txt");
                }
            }
        }

        $counter++;
    }

    $counter = 0; // Resetting incrementer

    // Adding error messages to the log
    foreach($arrayOfErrorMessage as $value)
    {
        if($value != "undefined")
        {
            // If a mile marker was added, this would only be the first mile marker
            if(strpos($value, "added") !== false)
            {
                $firstMileMarker = $mileMarkers[0];
                $firstMileMarkerText = file_get_contents("C:/xampp/htdocs/TonyAmos/Data/SortedData/$collection/$year/$filename/0_M$firstMileMarker.txt");
                $firstMileMarkerText = str_replace("* $firstMileMarker", "", $firstMileMarkerText); // Stripping mile marker from text to match the original file's text

                // Overwriting original file
                $originalfile = str_replace($firstMileMarkerText, "", $originalfile);
            }
            $errorMessage = "$value In C:/xampp/htdocs/TonyAmos/Data/SortedData/$collection/$year/$filename ";
            file_put_contents("C:/xampp/htdocs/TonyAmos/Data/Errors/$collection/ErrorLog.txt", $errorMessage . $date . PHP_EOL, FILE_APPEND);
        }
    }

    // Overwriting original file
    file_put_contents($originalFilePath, $originalfile);

    // Checking to see if the original file has any data in it
    if(filesize($originalFilePath) > 0)
    {
        array_push($arrayOfErrorMessage, "File still has data in it. In $originalFilePath ");
        $errorMessage = "File still has data in it. In $originalFilePath ";
        file_put_contents("C:/xampp/htdocs/TonyAmos/Data/Errors/$collection/ErrorLog.txt", $errorMessage . $date . PHP_EOL, FILE_APPEND);
    }
}

// For CSV generation, doing entire year server side
else if(isset($_POST["arrayOfPrimaryKeys"]) && isset($_POST["year"]))
{
    $year = $_POST["year"];

    $arrayOfPrimaryKeys = json_decode($_POST["arrayOfPrimaryKeys"]);

    $csvColumns = "\"bc_ID\",\"birdcode\",\"bc_observedamount\",\"bc_distance\",\"bc_yearday\",\"bc_julianday\",\"milemarker\",\"bc_duplicate_milemarker\"\n";

    $dir = "C:/xampp/htdocs/TonyAmos/CSV/BCHobs2/$year";
    mkdir($dir);

    foreach($arrayOfPrimaryKeys as $day)
    {
        $data = $DB->GET_CSV_TABLE($dbName, $day);
        $yearDay = "";

        $csvAsString = $csvColumns;

        // Generating the CSV file by looping through each element
        foreach($data as $element)
        {
            // If it is a duplicate milemarker
            if((int) $element["bc_duplicate_milemarker"] == 1)
            {
                $csvAsString .= "\"" . $element["bc_ID"] . "\",\"" . $element["birdcode"] . "\",\"" . $element["bc_observedamount"]
                    . "\",\"" . $element["bc_distance"] . "\",\"" . $element["bc_yearday"] . "\",\"" . $element["bc_julianday"] . "\",\"" .
                    $element["milemarker"] . "\",\"yes\"\n";
                $yearDay = $element["bc_yearday"];
            }

            else
            {
                $csvAsString .= "\"" . $element["bc_ID"] . "\",\"" . $element["birdcode"] . "\",\"" . $element["bc_observedamount"]
                    . "\",\"" . $element["bc_distance"] . "\",\"" . $element["bc_yearday"] . "\",\"" . $element["bc_julianday"] . "\",\"" .
                    $element["milemarker"] . "\",\"no\"\n";
                $yearDay = $element["bc_yearday"];
            }
        }

        $filepath = $dir . "/" . $yearDay . ".csv";
        file_put_contents($filepath, $csvAsString);

        $csvAsString = "";
    }
}

// Write to any file by specifying the text, filetype, path, and the filename
else if(isset($_POST["text"]) && isset($_POST["filetype"]) && isset($_POST["path"]) && isset($_POST["filename"]))
{
    $text = $_POST["text"];
    $filetype = $_POST["filetype"];
    $path = $_POST["path"];
    $filename = $_POST["filename"];

    $path .= "/$filename";

    if(file_put_contents($path, $text))
    {
        echo true;
    }

    else
    {
        echo false;
    }
}

else
{
    echo "Something went wrong in WriteToFile.php.";
}
?>