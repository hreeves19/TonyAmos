<?php
/**
 * Created by PhpStorm.
 * User: hreeves
 * Date: 8/16/2018
 * Time: 10:22 AM
 */
require_once '../../TonyAmos/Classes/DBHelper.php';

class TonyDBHelper
{
    private $dbhelper;
    private $arrayDays;
    private $arrayDaysMileMarker;
    private $arrayOfBirdCodes;
    private $arrayOfBirdIds;
    private $allFiles;
    public $collection = "BCHobs2";

    /**
     * @return mixed
     */
    public function getAllFiles()
    {
        return $this->allFiles;
    }

    /**
     * TonyDBHelper constructor.
     * @param $dbhelper
     */
    public function __construct()
    {
        $this->dbhelper = new DBHelper();
        $this->arrayDays = array();
        $this->arrayDaysMileMarker = array();
        $this->arrayOfBirdCodes = array();
        $this->arrayOfBirdIds = array();
    }

    /**
     * @return DBHelper
     */
    public function getDbhelper()
    {
        return $this->dbhelper;
    }

    /**
     * @return mixed
     */
    public function getArrayOfBirdIds()
    {
        return $this->arrayOfBirdIds;
    }

    /**
     * @return mixed
     */
    public function getArrayOfBirdCodes()
    {
        return $this->arrayOfBirdCodes;
    }

    /**
     * @return array
     */
    public function getArrayDaysMileMarker()
    {
        return $this->arrayDaysMileMarker;
    }

    public function GET_DAY_AND_DATE($dbName, $year)
    {
        $data = array();

        // Getting connection
        $mysqli = new mysqli($this->dbhelper->getHost(), $this->dbhelper->getUser(), $this->dbhelper->getPwd(), $dbName);

        // Checking to see if the connection failed
        if($mysqli->connect_errno)
        {
            echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
            return false;
        }

        $sql = "SELECT `bc_date_ID`, `bc_date`, `bc_yearday` FROM `bc_date` WHERE `bc_date` LIKE \"%$year%\"";

        $response = $mysqli->query($sql);

        if ($response)
        {
            while($row = mysqli_fetch_assoc($response))
            {
                $data[] = $row;
            }
        }

        else
        {
            echo "Error: " . $sql . "<br>" . $mysqli->error;
        }

        $mysqli->close();

        return $data;
    }

    public function INSERT_INTO_BIRD_CODES($id, $birdCode, $birdCodeDescription, $dbName, $tableName)
    {
        // Getting connection
        $mysqli = new mysqli($this->dbhelper->getHost(), $this->dbhelper->getUser(), $this->dbhelper->getPwd(), $dbName);

        // Checking to see if the connection failed
        if($mysqli->connect_errno)
        {
            echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
            return false;
        }

        // Check to see if prepare statement works
        if (!($stmt = $mysqli->prepare("INSERT INTO `birdcodes` (`codeID`, `birdcode`, `birdcodedescription`) VALUES (?,?,?)")))
        {
            echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
            return false;
        }

        // Check to see if binding the parameters works
        if (!$stmt->bind_param("sss", $ids, $birdCode, $birdCodeDescription))
        {
            echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
            return false;
        }

        // Executing prepared statement
        if (!$stmt->execute())
        {
            echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
            return false;
        }

        // Closing db connection
        mysqli_close($mysqli);
        return true;
    }

    public function INSERT_INTO_BC_DATE($yearday, $date, $julianday, $milemarker, $dbname)
    {
        // Getting connection
        $mysqli = new mysqli($this->dbhelper->getHost(), $this->dbhelper->getUser(), $this->dbhelper->getPwd(), $dbname);

        // Checking to see if the connection failed
        if($mysqli->connect_errno)
        {
            echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
            return false;
        }

        $sql = "INSERT INTO `bc_date` (`bc_yearday`, `bc_date`,`bc_julianday`) VALUES (\"$yearday\", \"$date\", $julianday)";

        if ($mysqli->query($sql) === TRUE) {
            echo "New record created successfully";
        }

        else
        {
            echo "Error: " . $sql . "<br>" . $mysqli->error;

            // Closing database connection
            $mysqli->close();

            return false;
        }

        $mysqli->close();

        return true;
    }

    public function INSERT_INTO_BC_OBSERVATIONS($code, $amountFound, $date, $julianday, $distance, $milemarker, $day, $dup, $dbname)
    {
        // Getting connection
        $mysqli = new mysqli($this->dbhelper->getHost(), $this->dbhelper->getUser(), $this->dbhelper->getPwd(), $dbname);

        // Checking to see if the connection failed
        if($mysqli->connect_errno)
        {
            echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
            return false;
        }

        if((int) $distance >= 0 && isset($distance))
        {
            $sql = "INSERT INTO `bc_observations`(`bc_observedamount`, `bc_distance`, `bc_duplicate_milemarker`, `bc_birdcode_id`, `bc_date_id`, `bc_milemarker_id`" .
                ") VALUES ($amountFound, $distance, $dup ,(SELECT `birdcodeID` FROM `birdcodes` WHERE `birdcode` LIKE \"$code\"),"
                . " (SELECT `bc_date_ID` FROM `bc_date` WHERE `bc_julianday` = $julianday), (SELECT `milemarkerID` FROM `milemarkers` WHERE ".
        "`milemarker` = $milemarker))";

        }


        else
        {
            $sql = "INSERT INTO `bc_observations`(`bc_observedamount`, `bc_duplicate_milemarker`, `bc_birdcode_id`, `bc_date_id`" .
                ", `bc_milemarker_id`) VALUES ($amountFound, $dup, (SELECT `birdcodeID` FROM `birdcodes` WHERE `birdcode` LIKE \"$code\"),"
                . " (SELECT `bc_date_ID` FROM `bc_date` WHERE `bc_julianday` = $julianday), (SELECT `milemarkerID` FROM `milemarkers` WHERE ".
        "`milemarker` = $milemarker))";
        }


        if ($mysqli->query($sql) === TRUE)
        {
            echo "New record created successfully";
        }

        else
        {
            echo "Error: " . $sql . "<br>" . $mysqli->error;

            // Closing database connection
            $mysqli->close();

            return false;
        }

        $mysqli->close();

        return true;
    }

    public function UPDATE_BC_DATE($dbName, $data, $primaryKey)
    {
        // Getting connection
        $mysqli = new mysqli($this->dbhelper->getHost(), $this->dbhelper->getUser(), $this->dbhelper->getPwd(), $dbName);

        // Checking to see if the connection failed
        if($mysqli->connect_errno)
        {
            echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
            return false;
        }

        $sql = "UPDATE `bc_date` SET `bc_date_object` = \"$data\" WHERE `bc_date_ID` = $primaryKey";

        // Checking to see if it works
        if($mysqli->query($sql) === TRUE)
        {
            echo "Record updated successfully";
        }

        else
        {
            echo "Error updating record: " . $mysqli->error;
        }

        $mysqli->close();
    }

    public function GET_BC_DATE($dbName)
    {
        $dateArray = array();

        // Getting connection
        $mysqli = new mysqli($this->dbhelper->getHost(), $this->dbhelper->getUser(), $this->dbhelper->getPwd(), $dbName);

        // Checking to see if the connection failed
        if($mysqli->connect_errno)
        {
            echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
            return false;
        }

        $sql = "SELECT `bc_date_ID`, `bc_date` FROM `bc_date`";

        $response = $mysqli->query($sql);

        if ($response)
        {
            while($row = $response->fetch_assoc())
            {
                /*$dateArray[] = $row;*/
                $dateArray[$row["bc_date_ID"]] = $row["bc_date"];
            }
        }

        else
        {
            echo "Error: " . $sql . "<br>" . $mysqli->error;
        }

        $mysqli->close();

        return $dateArray;
    }

    public function POPULATE_DATA_TABLE($dbName, $year)
    {
        $data = array();

        // Getting connection
        $mysqli = new mysqli($this->dbhelper->getHost(), $this->dbhelper->getUser(), $this->dbhelper->getPwd(), $dbName);

        // Checking to see if the connection failed
        if($mysqli->connect_errno)
        {
            echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
            return false;
        }

        $sql = "select `bc_ID`, b.`birdcode`, `bc_observedamount`, `bc_distance`, p.`bc_yearday`, p.`bc_date`, p.`bc_julianday`,  g.`milemarker` from `bc_observations` w" .
                " left join `bc_date` p on p.`bc_date_ID` = w.`bc_date_id`" .
                " left join `milemarkers` g on g.`milemarkerID` = w.`bc_milemarker_id`" .
                " left join `birdcodes`b on b.`birdcodeID` = w.`bc_birdcode_id`"
                . " WHERE p.`bc_date` LIKE \"%$year%\"";

        $response = $mysqli->query($sql);

        if ($response)
        {
            while($row = mysqli_fetch_assoc($response))
            {
                $data[] = $row;
            }
        }

        else
        {
            echo "Error: " . $sql . "<br>" . $mysqli->error;
        }

        $mysqli->close();

        return $data;
    }

    public function GET_CSV_TABLE($dbName, $primaryKey)
    {
        $data = array();

        // Getting connection
        $mysqli = new mysqli($this->dbhelper->getHost(), $this->dbhelper->getUser(), $this->dbhelper->getPwd(), $dbName);

        // Checking to see if the connection failed
        if($mysqli->connect_errno)
        {
            echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
            return false;
        }

        $sql = "select `bc_ID`, b.`birdcode`, `bc_observedamount`, `bc_distance`, p.`bc_yearday`, p.`bc_julianday`,  g.`milemarker`, `bc_duplicate_milemarker` from `bc_observations` w" .
            " left join `bc_date` p on p.`bc_date_ID` = w.`bc_date_id`" .
            " left join `milemarkers` g on g.`milemarkerID` = w.`bc_milemarker_id`" .
            " left join `birdcodes`b on b.`birdcodeID` = w.`bc_birdcode_id`"
            . " WHERE p.`bc_date_ID` = $primaryKey";

        // Executing query and getting the response
        $response = $mysqli->query($sql);

        // If the response was successful
        if ($response)
        {
            while($row = mysqli_fetch_assoc($response))
            {
                $data[] = $row;
            }
        }

        else
        {
            echo "Error: " . $sql . "<br>" . $mysqli->error;
        }

        $mysqli->close();

        return $data;
    }

    public function POPULATE_DATA_TABLE_DAY($dbName, $primaryKey, $year)
    {
        $data = array();

        // Getting connection
        $mysqli = new mysqli($this->dbhelper->getHost(), $this->dbhelper->getUser(), $this->dbhelper->getPwd(), $dbName);

        // Checking to see if the connection failed
        if($mysqli->connect_errno)
        {
            echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
            return false;
        }

        // If it is an integer
        if((int) $primaryKey != 0)
        {
            $sql = "select `bc_ID`, b.`birdcode`, `bc_observedamount`, `bc_distance`, p.`bc_yearday`, p.`bc_julianday`,  g.`milemarker` from `bc_observations` w" .
                " left join `bc_date` p on p.`bc_date_ID` = w.`bc_date_id`" .
                " left join `milemarkers` g on g.`milemarkerID` = w.`bc_milemarker_id`" .
                " left join `birdcodes`b on b.`birdcodeID` = w.`bc_birdcode_id`"
                . " WHERE p.`bc_date_ID` = $primaryKey";
        }

        else
        {
            $sql = "select `bc_ID`, b.`birdcode`, `bc_observedamount`, `bc_distance`, p.`bc_yearday`, p.`bc_julianday`,  g.`milemarker` from `bc_observations` w" .
                " left join `bc_date` p on p.`bc_date_ID` = w.`bc_date_id`" .
                " left join `milemarkers` g on g.`milemarkerID` = w.`bc_milemarker_id`" .
                " left join `birdcodes`b on b.`birdcodeID` = w.`bc_birdcode_id`"
                . " WHERE p.`bc_date` LIKE \"%$year%\"";
        }

        // Executing query and getting the response
        $response = $mysqli->query($sql);

        // If the response was successful
        if ($response)
        {
            while($row = mysqli_fetch_assoc($response))
            {
                $data[] = $row;
            }
        }

        else
        {
            echo "Error: " . $sql . "<br>" . $mysqli->error;
        }

        $mysqli->close();

        return $data;
    }

    public function POPULATE_DATA_TABLE_WHERE($dbName, $statement)
    {
        $data = array();

        // Getting connection
        $mysqli = new mysqli($this->dbhelper->getHost(), $this->dbhelper->getUser(), $this->dbhelper->getPwd(), $dbName);

        // Checking to see if the connection failed
        if($mysqli->connect_errno)
        {
            echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
            return false;
        }

        $sql = "select `bc_ID`, b.`birdcode`, `bc_observedamount`, `bc_distance`, p.`bc_date`, p.`bc_julianday`,  g.`milemarker` from `bc_observations` w" .
            " left join `bc_date` p on p.`bc_date_ID` = w.`bc_date_id`" .
            " left join `milemarkers` g on g.`milemarkerID` = w.`bc_milemarker_id`" .
            " left join `birdcodes`b on b.`birdcodeID` = w.`bc_birdcode_id`"
            . " WHERE $statement";

        // Executing query and getting the response
        $response = $mysqli->query($sql);

        // If the response was successful
        if ($response)
        {
            while($row = mysqli_fetch_assoc($response))
            {
                $data[] = $row;
            }
        }

        else
        {
            echo "Error: " . $sql . "<br>" . $mysqli->error;
        }

        $mysqli->close();

        return $data;
    }

    public function GET_ALL_BIRD_CODES($dbName)
    {
        // Getting connection
        $mysqli = new mysqli($this->dbhelper->getHost(), $this->dbhelper->getUser(), $this->dbhelper->getPwd(), $dbName);

        // Checking to see if the connection failed
        if($mysqli->connect_errno)
        {
            echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
            return false;
        }

        // Creating select statement
        $sql = "SELECT birdcode FROM birdcodes";
        $result = $mysqli->query($sql);

        if ($result->num_rows > 0)
        {

            // output data of each row
            while($row = $result->fetch_assoc())
            {
                array_push($this->arrayOfBirdCodes, $row["birdcode"]);
            }
        }

        else
        {
            echo "0 results";
        }

        // Closing db connection
        mysqli_close($mysqli);
        return true;
    }

    public function GET_ALL_BIRD_IDS($dbName)
    {
        // Getting connection
        $mysqli = new mysqli($this->dbhelper->getHost(), $this->dbhelper->getUser(), $this->dbhelper->getPwd(), $dbName);

        // Checking to see if the connection failed
        if($mysqli->connect_errno)
        {
            echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
            return false;
        }

        // Creating select statement
        $sql = "SELECT codeID FROM birdcodes";
        $result = $mysqli->query($sql);

        if ($result->num_rows > 0)
        {

            // output data of each row
            while($row = $result->fetch_assoc())
            {
                array_push($this->arrayOfBirdIds, $row["codeID"]);
            }
        }

        else
        {
            echo "0 results";
        }

        // Closing db connection
        mysqli_close($mysqli);
        return true;
    }

    public function GET_YEAR_FILE($year)
    {
        /*C:/xampp2/htdocs/BandoCat/tonyamos/SortedData/BCHobs2/BDZ$year*/
        $dir = "C:/xampp/htdocs/TonyAmos/Data/SortedData/$this->collection/BDZ$year";

        // Open a directory, and read its contents
        if (is_dir($dir))
        {
            if ($dh = opendir($dir))
            {
                // File is a string with the name of the folder of days
                while (($file = readdir($dh)) !== false)
                {
                    echo "filename:" . $file . "<br>";
                    array_push($this->arrayDays, $file);
                }
                closedir($dh);
            }
        }
    }

    public function GET_PRIMARYKEYS_OF_DAY_FROM_YEAR($dbName, $year)
    {
        $data = array();

        // Getting connection
        $mysqli = new mysqli($this->dbhelper->getHost(), $this->dbhelper->getUser(), $this->dbhelper->getPwd(), $dbName);

        // Checking to see if the connection failed
        if($mysqli->connect_errno)
        {
            echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
            return false;
        }

        $sql = "SELECT `bc_date_ID` FROM `bc_date` WHERE `bc_date` LIKE \"%$year%\"";

        // Executing query
        $response = $mysqli->query($sql);

        // If the response has results and no errors
        if($response)
        {
            while($row = mysqli_fetch_assoc($response))
            {
                /*$data[] = $row;*/
                array_push($data, $row["bc_date_ID"]);
            }
        }

        else
        {
            echo "Error: " . $sql . "<br>" . $mysqli->error;
        }

        $mysqli->close();

        return $data;
    }

    public function GET_DDL_FILES_BY_YEAR($year)
    {
        // Path to the directory
        $dir = "C:/xampp/htdocs/TonyAmos/Data/SortedData/$this->collection/BDZ$year";

        // Open a directory, and read its contents
        if (is_dir($dir))
        {
            if ($dh = opendir($dir))
            {
                echo "Files found in $year <select id = 'ddlDaysToInsert' onchange=''>";
                echo "<option value ='" . -1 . "'>Unselected</option>";

                // File is a string with the name of the folder of days
                while (($file = readdir($dh)) !== false)
                {
                    if(preg_match('/\w/', $file) !== 1)
                    {
                        continue;
                    }

                    else
                    {
                        echo "<option value='" . $file . "'>$file</option>";
                    }
                }
                echo "</select>";
                echo "<button type=\"button\" onclick='insertOneDayToDB()'>Insert file</button>";
                closedir($dh); // Closing directory
            }
        }
    }

    public function GET_MILE_MARKER_FILE_FROM_YEAR($year)
    {
        /*C:/xampp2/htdocs/BandoCat/tonyamos/SortedData/BCHobs2/BDZ$year*/

        // Checking to see if array of days exists and is set
        if(!empty($this->arrayDays) && isset($this->arrayDays))
        {
            foreach($this->arrayDays as $filename)
            {
                $dir = "C:/xampp/htdocs/TonyAmos/Data/SortedData/$this->collection/BDZ$year/$filename";

                // Checking to see if file name is correct or not, skipping the . and ..
                if(preg_match('/\w/', $filename) !== 1)
                {
                    /*echo "BDZ$year/$filename failed <br>";*/
                    continue;
                }

                /*echo "Directory: $year/$filename ";*/

                // Open a directory, and read its contents
                if (is_dir($dir))
                {
                    if ($dh = opendir($dir))
                    {

                        // File is a string with the name of the folder of days
                        while (($file = readdir($dh)) !== false)
                        {
                            /*echo "filename:" . $file . "<br>";*/
                            /*$this->arrayDaysMileMarker[$dir . "/" . $file] = */
                            // Checking to see if file name is correct or not, skipping the . and ..
                            if(preg_match('/\w/', $file) !== 1)
                            {
                                /*echo "BDZ$year/$file failed <br>";*/
                                continue;
                            }

                            else
                            {
                                $this->arrayDaysMileMarker[$dir . "/$file"] = file_get_contents($dir . "/" . $file);
                            }
                        }
                        closedir($dh);
                    }
                }
            }
        }

        else
        {
            echo "Failed....";
        }
    }

    public function GET_MILE_MARKER_FILE_FROM_DAY($year, $filename)
    {
        // Directory to the day
        $dir = "C:/xampp/htdocs/TonyAmos/Data/SortedData/$this->collection/BDZ$year/$filename";

        // Open a directory, and read its contents
        if (is_dir($dir))
        {
            if ($dh = opendir($dir))
            {

                // File is a string with the name of the folder of days
                while (($file = readdir($dh)) !== false)
                {
                    // Checking to see if file name is correct or not, skipping the . and ..
                    if(preg_match('/\w/', $file) !== 1)
                    {
                        continue;
                    }

                    else
                    {
                        $this->arrayDaysMileMarker[$dir . "/$file"] = file_get_contents($dir . "/" . $file);
                    }
                }

                // Closing directory
                closedir($dh);
            }
        }
    }

    public function GET_ALL_FILE_NAMES()
    {
        // Initializing years and array to hold the result of the search
        $startYear = 1985;
        $endYear = 2010;
        $year = $startYear;
        $counter = 0;

        // Looping through every year
        while($year <= $endYear)
        {
            // Directory to the first year
            $dir = "C:/xampp/htdocs/TonyAmos/Data/SortedData/$this->collection/BCHobs2/BDZ$year";

            // Open a directory, and read its contents
            if (is_dir($dir))
            {
                // Checking to see if the $dir can be opened
                if ($dh = opendir($dir))
                {

                    // File is a string with the name of the folder of days
                    while (($file = readdir($dh)) !== false)
                    {
                        // Checking to see if file name is correct or not, skipping the . and ..
                        if(preg_match('/\w/', $file) !== 1)
                        {
                            continue;
                        }

                        else
                        {
                            //$this->arrayDaysMileMarker[$dir . "/$file"] = file_get_contents($dir . "/" . $file);
                            $this->allFiles[$counter] = $year . "/" . $file;
                            $counter++;
                        }
                    }

                    // Closing directory
                    closedir($dh);
                }
            }

            // Move up in a year, even if file cannot be opened
            $year++;
        }
    }

    public function GET_DDL_YEARS($dbName, $year)
    {
        // Getting connection
        $mysqli = new mysqli($this->dbhelper->getHost(), $this->dbhelper->getUser(), $this->dbhelper->getPwd(), $dbName);

        // Checking to see if the connection failed
        if($mysqli->connect_errno)
        {
            echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
            return false;
        }

        // Creating select statement
        $sql = "SELECT * FROM `bc_date` WHERE `bc_date` LIKE \"%$year%\" ORDER BY `bc_date` ASC";
        $result = $mysqli->query($sql);

        if ($result->num_rows > 0)
        {
            echo "Days in $year <select id = 'ddlDays'>";
            echo "<option value ='" . -1 . "'>Unselected</option>";
            /*echo "<option value ='all'>All Days</option>";*/
            // output data of each row
            while($row = $result->fetch_assoc())
            {
                echo "<option value ='" . $row["bc_date_ID"] . "'>"
                    . $row["bc_date"] . "</option>";
            }
            echo "</select>";
        }

        else
        {
            // Closing database connection
            $mysqli->close();

            return false;
        }

        // Closing db connection
        mysqli_close($mysqli);
        return true;
    }

    public function GET_DDL_DAYS($dbName, $year, $delete)
    {
        // Getting connection
        $mysqli = new mysqli($this->dbhelper->getHost(), $this->dbhelper->getUser(), $this->dbhelper->getPwd(), $dbName);

        // Checking to see if the connection failed
        if($mysqli->connect_errno)
        {
            echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
            return false;
        }

        // Creating select statement
        $sql = "SELECT * FROM `bc_date` WHERE `bc_date` LIKE \"%$year%\" ORDER BY `bc_date` ASC";
        $result = $mysqli->query($sql);

        if ($result->num_rows > 0)
        {
            // Initializing the drop down list and setting the first few values
            echo "Days in $year <select id = 'ddlDays'>";
            echo "<option value ='" . -1 . "'>Unselected</option>";
            echo "<option value ='all'>All Days</option>";

            // output data of each row
            while($row = $result->fetch_assoc())
            {

                echo "<option value ='" . $row["bc_date_ID"] . "'>"
                    . $row["bc_yearday"] . "</option>";
            }
            echo "</select>";

            // Deleting from the DB has been called
            if($delete === true)
            {
                // Adding delete button
                echo "<button type=\"button\" onclick='deleteFromDB()'>Delete file </button>";
            }

            // For CSV generation
            else
            {
                // Adding delete button
                echo "<button type=\"button\" onclick='generateCSV()'>Generate CSV for day selected</button>";
            }
        }

        // Closing db connection
        mysqli_close($mysqli);
    }

    public function GET_DDL_BIRD_CODES($dbName)
    {
        // Getting connection
        $mysqli = new mysqli($this->dbhelper->getHost(), $this->dbhelper->getUser(), $this->dbhelper->getPwd(), $dbName);

        // Checking to see if the connection failed
        if($mysqli->connect_errno)
        {
            echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
            return false;
        }

        // Creating select statement
        $sql = "SELECT `birdcodeID`, `birdcode` FROM `birdcodes` ORDER BY `birdcode` ASC";
        $result = $mysqli->query($sql);

        if ($result->num_rows > 0)
        {
            // Initializing the drop down list and setting the first few values
            echo "<select id = 'ddlBirdCodes'>";
            echo "<option value ='" . -1 . "'>All</option>";

            // output data of each row
            while($row = $result->fetch_assoc())
            {
                // Do not allow birdcode DEAD
                if(strcmp($row["birdcode"], "DEAD"))
                {
                    echo "<option value ='" . $row["birdcodeID"] . "'>"
                        . $row["birdcode"] . "</option>";
                }
            }
            echo "</select>";
        }

        // Closing db connection
        mysqli_close($mysqli);
    }

    public function GET_DDL_MILEMARKERS($dbName)
    {
        // Getting connection
        $mysqli = new mysqli($this->dbhelper->getHost(), $this->dbhelper->getUser(), $this->dbhelper->getPwd(), $dbName);

        // Checking to see if the connection failed
        if($mysqli->connect_errno)
        {
            echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
            return false;
        }

        // Creating select statement
        $sql = "SELECT `milemarkerID`, `milemarker` FROM `milemarkers`";
        $result = $mysqli->query($sql);

        if ($result->num_rows > 0)
        {
            // Initializing the drop down list and setting the first few values
            echo "<select id = 'ddlMilemarkers'>";
            echo "<option value ='" . -1 . "'>All</option>";

            // output data of each row
            while($row = $result->fetch_assoc())
            {
                echo "<option value ='" . $row["milemarkerID"] . "'>"
                    . $row["milemarker"] . "</option>";
            }
            echo "</select>";
        }

        // Closing db connection
        mysqli_close($mysqli);
    }

    public function GET_DDL_MONTH($dbName, $year)
    {
        $monthArray = [];

        // Getting connection
        $mysqli = new mysqli($this->dbhelper->getHost(), $this->dbhelper->getUser(), $this->dbhelper->getPwd(), $dbName);

        // Checking to see if the connection failed
        if($mysqli->connect_errno)
        {
            echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
            return false;
        }

        // Creating select statement
        $sql = "SELECT `bc_date_ID`, `bc_date` FROM `bc_date` WHERE `bc_date` LIKE \"%$year%\" ORDER BY `bc_date` ASC";
        $result = $mysqli->query($sql);

        if ($result->num_rows > 0)
        {
            // Initializing the drop down list and setting the first few values
            echo "Search Month: <select id = 'ddlMonth'>";
            echo "<option value ='" . "__-__-$year" . "'>Entire Year</option>";

            // output data of each row
            while($row = $result->fetch_assoc())
            {
                // Getting month to an integer
                $month = $row["bc_date"];
                $month = substr($month, 0, 2);
                $value = $month . "-__-$year";
                $month = (int) $month;

                // Checking to see if in the array
                if(in_array($month, $monthArray))
                {
                    continue;
                }

                else {
                    // Storing number in array
                    array_push($monthArray, $month);

                    // Creating month as date object
                    $dateObj   = DateTime::createFromFormat('!m', $month);
                    $monthName = $dateObj->format('F'); // March

                    echo "<option value ='" . $value . "'>"
                        . $monthName . "</option>";
                }
            }
            echo "</select>";
        }

        // Closing db connection
        mysqli_close($mysqli);
    }

    // This function will delete the day from the bc_date table
    public function DELETE_DAY($dbName, $dayPrimaryKey)
    {
        // Getting connection
        $mysqli = new mysqli($this->dbhelper->getHost(), $this->dbhelper->getUser(), $this->dbhelper->getPwd(), $dbName);

        // Checking to see if the connection failed
        if($mysqli->connect_errno)
        {
            echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
            return false;
        }

        $sql = "DELETE FROM `bc_date` WHERE `bc_date_ID` = $dayPrimaryKey";

        // Checking to see if the query was successful
        if ($mysqli->query($sql) === TRUE)
        {
            echo "Record successfully deleted from bc_date table.<br>";
        }

        else
        {
            echo "Error: " . $sql . "<br>" . $mysqli->error;

            // Closing database connection
            $mysqli->close();

            return false;
        }

        // Closing database connection
        $mysqli->close();

        return true;
    }

    // Will delete all records from a particular day. The foreign key is the primary key of the bc_date table.
    public function DELETE_ALL_OBSERVATIONS_BY_DAY($dbName, $foreignkey)
    {
        // Getting connection
        $mysqli = new mysqli($this->dbhelper->getHost(), $this->dbhelper->getUser(), $this->dbhelper->getPwd(), $dbName);

        // Checking to see if the connection failed
        if($mysqli->connect_errno)
        {
            echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
            return false;
        }

        $sql = "DELETE FROM `bc_observations` WHERE `bc_date_id` = $foreignkey";

        // Checking to see if the query was successful
        if ($mysqli->query($sql) === TRUE)
        {
            echo "Records successfully deleted from bc_observations table.<br>";
        }

        else
        {
            echo "Error: " . $sql . "<br>" . $mysqli->error;

            // Closing database connection
            $mysqli->close();
            return false;
        }

        // Closing database connection
        $mysqli->close();
        return true;
    }
}