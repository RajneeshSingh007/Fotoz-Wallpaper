<?php

/**
 * Created by IntelliJ IDEA.
 * User: Rajneesh
 * Date: 07-Oct-17
 * Time: 9:51 PM
 */
class Connection{

    /**
     * Connect to database
     */
    public function Connect(){

        require_once('Config.php');

        //connect to database
        $Conn = new mysqli(SERVERNAME, USER, PASSWORD, DBNAME);

        return $Conn;
    }


}

?>