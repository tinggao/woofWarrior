<?php
/**
 * Created by PhpStorm.
 * User: Haziq
 * Date: 10/20/2015
 * Time: 11:47 AM
 */

class DBConnection
{

    public function DBConnect()
    {
        $host = "localhost";
        $username = "root";
        $password = '';
        $dbname = "woofworrior";
        $result = mysqli_connect($host,$username,$password,$dbname);
        if($result)
        {
            return $result;
        }
        else
        {
            return mysqli_error($result);
        }

    }

    public function Select($query)
    {
        $connection = $this->DBConnect();
        $result = mysqli_query($connection, $query);
        if($result)
        {
            return $result;
        }
        else
        {
            return mysqli_error($connection);
        }

    }

    public function Fetch_Single_Value($query, $number)
    {
        $result = mysql_query($query);
        if(!$result){return "Fetch Method: ".mysql_error();}
        $DATA = mysql_fetch_row($result);
        $data = $DATA[$number];
        return $data;
    }



}

?>