<?php
//NOTE:
/*
- BEFORE EXECUTING QUERIES OR CALL SPs, MUST EITHER:
    + Use prepare statement
    + OR Sanitize input using mysqli_real_escape_string

- BIND PARAMETER: Types: s = string, i = integer, d = double,  b = blob
*/

//PLEASE USE THIS TEMPLATE TO COMMENT ON FUNCTIONS
/**********************************************
Function:
Description:
Parameter(s):
Return value(s):
 ***********************************************/

class DBHelper
{
    //Members
     static protected $ini_dir = "Bandocat_config\\bandoconfig.ini";
     protected $host;
     protected $user;
     protected $pwd;
     protected $maindb;
     protected $conn;

    //Getter and setters

    function __construct()
    {
     $root = substr(getcwd(),0,strpos(getcwd(),"htdocs\\")); //point to xampp// directory
     $config = parse_ini_file($root . DBHelper::$ini_dir);
     $this->host = $config['servername'];
     $this->user = $config['username'];
     $this->pwd = $config['password'];
     $this->maindb = $config['dbname'];
        /*if not currently connected, attempt to connect to DB*/
        if ($this->getConn() == null)
            $this->DB_CONNECT(null);
    }

    /**
     * @return mixed
     */
    public function getConn()
    {
        return $this->conn;
    }

    /**
     * @param mixed $conn
     */
    public function setConn($conn)
    {
        $this->conn = $conn;
    }

    /**********************************************
     * Function: DB_CONNECT
     * Description: Connect to XAMPP db. localhost/phpadmin All functions connect to db using this.
     * Parameter(s):
     * $db (string) - name of database to be connected to
     * Return value(s):
     * $conn (object) - return connection object if success
     * - return -1 if failed
     ***********************************************/

    function DB_CONNECT($db)
    {
        if ($db == "" || $db == null) //empty parameter = default = bandocatdb
            $db = "bandocatdb";
        /* assign conn as a PHP Data Object, concat the host, user and pwd */
        $this->conn = new PDO('mysql:host=' . $this->getHost() . ';dbname=' . $db, $this->getUser(), $this->getPwd());
        return 0;
    }

    /**
     * @return string
     */
    public function getHost()
    {
        return $this->host;
    }


    //Constructor

    /**
     * @return string
     */
    public function getUser()
    {
        return $this->user;
    }

    //IMPORTANT: Default Host, username, password can be changed here!

    /**
     * @return string
     */
    public function getPwd()
    {
        return $this->pwd;
    }

    function DB_CLOSE()
    {
        $this->setConn(null);
    }
}