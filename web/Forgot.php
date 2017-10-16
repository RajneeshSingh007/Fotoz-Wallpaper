<?php

    /**
     * Created by IntelliJ IDEA.
     * User: Coolalien
     * Date: 08-Oct-17
     * Time: 3:56 PM
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

    // check if data is set in the form
    if (isset($_POST['user_name']) && isset($_POST['user_opass']) && isset($_POST['user_npass'])) {

        $userName = $_POST['user_name'];
        $userOPass = $_POST['user_opass'];
        $userNPass = $_POST['user_npass'];

        // check if value is null or not
        if (empty($userName) || empty($userOPass) || empty($userNPass)) {
            $jsonData[] = array("Error" => TRUE, "error_msg" => "Form data is empty, Please check again");
        } else {
            //query to check userName in db
            $checkUserQuery = "SELECT * FROM user WHERE userName='$userName'";

            //fetch row of selected username
            $checkUserExist = $connect->query($checkUserQuery);

            $result = mysqli_fetch_array($checkUserExist);

            //if row is fetched successfully
            if ($result > 0) {
                $user = $result["userName"];
                $saltDb = $result["encyrptSalt"]; //got salt from selected user
                $encyrptPass = $result["encyrptPassword"]; //got encyrptPassword from selected user
                $decrypt = base64_encode(sha1($userOPass . $saltDb, true) . $saltDb); //decrypt

                if (($user == $userName) && ($encyrptPass == $decrypt)) {
                    //password protection
                    $sha = sha1(mt_rand()); //generate random sha1 key
                    //substring
                    $salt = substr($sha, 0, 10);
                    //encrypt password
                    $encrypt = base64_encode(sha1($userNPass . $salt, true) . $salt);

                    $sqlUpdate = "UPDATE user SET encyrptSalt='$salt', encyrptPassword = '$encrypt' WHERE userName ='$userName'";

                    $updateData = $connect->query($sqlUpdate);

                    if ($sqlUpdate) {
                        //reset password done so redirect to user dashboard
                        $login = "login.html";
                        header("Location: $login");
                    } else {
                        $jsonData[] = array("Error" => TRUE, "error_msg" => "Failed to reset password");
                    }
                } else {
                    $jsonData[] = array("Error" => TRUE, "error_msg" => "No user found");
                }
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