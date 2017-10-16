<?php

    /**
     * Created by IntelliJ IDEA.
     * User: Coolalien
     * Date: 08-Oct-17
     * Time: 3:56 PM
     */

    header("Content-type: text/javascript");

    session_start(); //session start

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

    // check if data is set in the form
    if (isset($_POST['user_name']) && isset($_POST['user_pass'])) {

        $userName = $_POST['user_name'];
        $userPass = $_POST['user_pass'];
        // check if value is null or not
        if (empty($userName) || empty($userPass)) {
            $jsonData[] = array("Error" => TRUE, "error_msg" => "Form data is empty, Please check again");
        } else {
            //query to check userName in db
            $checkUserQuery = "SELECT * FROM user WHERE userName='$userName'";

            //fetch row of selected username
            $checkUserExist = $connect->query($checkUserQuery);

            $result = mysqli_fetch_array($checkUserExist);

            //if row is fetched successfully
            if ($result > 0) {
                //email verify
                $activated = $result["is_activated"];
                if ($activated == 1) {
                    $salt = $result["encyrptSalt"]; //got salt from selected user
                    $encyrptPass = $result["encyrptPassword"]; //got encyrptPassword from selected user
                    $decrypt = base64_encode(sha1($userPass . $salt, true) . $salt); //decrypt

                    if ($encyrptPass == $decrypt) {
                        //login success
                        $jsonData[] = array("Error" => FALSE, "username" => $result["userName"], "email" => $result["userEmail"], "success_msg" => "Login Success");
                        //login details are right so redirect to user dashboard
                        $dashboard = "userdashboard.html";
                        header("Location: $dashboard");
                        $_SESSION['user'] = $result["userName"]; //session Data store
                    } else {
                        $jsonData[] = array("Error" => TRUE, "error_msg" => "Password incorrect");
                    }
                } else {
                    $jsonData[] = array("Error" => TRUE, "error_msg" => "Email verification left");
                }
            } else {
                $jsonData[] = array("Error" => TRUE, "error_msg" => "No user found");
            }
        }
    } else {
        $jsonData[] = array("Error" => TRUE, "error_msg" => "Required parameters is missing");
    }

    //encode into json and print
    echo json_encode($jsonData, JSON_PRETTY_PRINT);

    //close db connection
    $connect->close();

?>