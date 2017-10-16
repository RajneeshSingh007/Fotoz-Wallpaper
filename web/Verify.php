<?php
    /**
     * Created by IntelliJ IDEA.
     * User: Coolalien
     * Date: 11-Oct-17
     * Time: 9:33 PM
     */

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
    $verifyData = array();

    if (isset($_POST['verify_code']) && isset($_POST['url'])) {

        $code = $_POST['verify_code'];
        $currentUrl = $_POST['url'];

        if (empty($code) || empty($currentUrl)) {
            $verifyData[] = array("Error" => TRUE, "error_msg" => "Empty form data");
        } else {
            //substring url
            $subUser = explode("=", $currentUrl);

            //access username from url using end function
            $userName = end($subUser);

            //query user's detail
            $dataQuery = $connect->query("SELECT * FROM user WHERE userName='$userName'");

            //fetch row result in array
            $fetchResult = mysqli_fetch_array($dataQuery);

            if ($fetchResult > 0) {
                $userCode = $fetchResult["email_verify"];

                //check if both code are same or not
                if ($code == $userCode) {
                    //success so update activated flag for successfully login
                    $sqlUpdate = "UPDATE user SET is_activated=1 WHERE userName ='$userName'";

                    $queryAgain = $connect->query($sqlUpdate);

                    if ($queryAgain == TRUE) {
                        $verifyData[] = array("Error" => FALSE, "success_msg" => "Account activated");
                        //successful so redirect to home
                        $home = "login.html";
                        header("Location: $home");
                    } else {
                        $verifyData[] = array("Error" => TRUE, "error_msg" => "Account activation failed");
                    }
                } else {
                    $verifyData[] = array("Error" => TRUE, "error_msg" => "Verification code is wrong");
                }
            }
        }

    } else {
        $verifyData[] = array("Error" => TRUE, "error_msg" => "Required parameters is missing");
    }

    //encode into json and print
    echo json_encode($verifyData, JSON_PRETTY_PRINT);

    //close db connection
    $connect->close();

?>