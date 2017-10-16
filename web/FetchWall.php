<?php
    /**
     * Created by IntelliJ IDEA.
     * User: Coolalien
     * Date: 12-Oct-17
     * Time: 1:06 AM
     */


    header("Content-type: text/javascript");

    // call connection file
    require_once('Connection.php');

    //object of connection class
    $connection = new Connection();

    //call mysql db connection funtion
    $connect = $connection->Connect();

    // check connection
    if (!$connect) {
        echo "something went wrong !!";
        die(mysqli_connect_errno());
    }

    //array for user Register form
    $jsonData = array();

    //fetch all row from table
    $data = $connect->query("SELECT * FROM user WHERE 1 ");

    //loop through all rows and add those into array
    while ($fetchResult = mysqli_fetch_array($data)) {
        //add data into array
        $jsonData[] = array("Error" => FALSE, "username" => $fetchResult["userName"],
            "email" => $fetchResult["userEmail"], "wallpath" => $fetchResult["wallPath"],
            "wallname" => $fetchResult["wallName"]);
    }

    //encode into json and print
    echo json_encode($jsonData, JSON_PRETTY_PRINT);

    //close db connection
    $connect->close();

?>