<?php
/**
 * Created by PhpStorm.
 * User: hreeves
 * Date: 9/28/2018
 * Time: 8:23 AM
 */

require_once '../../TonyAmos/Classes/DBHelper.php';

class TonyQueryBuilder
{
    private $query;
    private $request;
    private $dbhelper;

    /**
     * TonyQueryBuilder constructor.
     * @param $query
     */
    public function __construct($request)
    {
        $this->request = $request;
        $this->dbhelper = new DBHelper();
    }
}