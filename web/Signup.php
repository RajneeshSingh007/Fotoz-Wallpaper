<?php

    /**
     * Created by IntelliJ IDEA.
     * User: Rajneesh Singh
     * Date: 07-Oct-17
     * Time: 10:15 PM
     */

    // call connection file
    require_once('Connection.php');
    require_once('email_verfiy.php');

    //object of email class
    $sendEmail = new email_verfiy();

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
    if (isset($_POST['user_name']) && isset($_POST['user_email']) && isset($_POST['user_pass']) && isset($_POST['user_cpass'])) {

        $userName = $_POST['user_name'];
        $userEmail = $_POST['user_email'];
        $userPass = $_POST['user_pass'];
        $usercPass = $_POST['user_cpass'];

        //query to get userName from db
        $checkUserQuery = "SELECT userName FROM user WHERE userName='$userName'";

        //fetch row of selected username
        $checkUserExist = $connect->query($checkUserQuery);

        //if row is fetched successfully
        if ($result = mysqli_num_rows($checkUserExist) > 0) {
            $jsonData[] = array("Error" => TRUE, "error_msg" => "User is already registered");
        } else {
            //check if email is valid
            $filterEmail = filter_var($userEmail, FILTER_VALIDATE_EMAIL);
            if ($filterEmail == TRUE) {
                //check if both password is equal or not
                if ($userPass == $usercPass) {
                    //generate username based md5 unique id
                    $uniqueID = uniqid($userName);
                    //password protection
                    $sha = sha1(mt_rand()); //generate random sha1 key
                    //substring sha
                    $salt = substr($sha, 0, 10);
                    //encryption using base64 by using $salt substring
                    $encrypt = base64_encode(sha1($userPass . $salt, true) . $salt);
                    //actviated value (@DEFAULT 0) for email verification;
                    $activate = 0;
                    // email_verify code
                    $emailVerify = mt_rand(1000, 9999);

                    //insert query
                    $userInsertQuery = "INSERT INTO user (user_uniqueId, userName, userEmail, encyrptPassword,encyrptSalt,created_at,updated_at,is_activated,email_verify) 
                                            VALUES (
                                            '$uniqueID',
                                            '$userName',
                                            '$userEmail',
                                            '$encrypt',
                                            '$salt',
                                            NOW(),
                                            NULL ,
                                            '$activate',
                                            '$emailVerify')";

                    //insert data into table user
                    $insertData = $connect->query($userInsertQuery);

                    //query user's detail
                    $dataQuery = $connect->query("SELECT * FROM user WHERE userName='$userName'");

                    //fetch all column and it's data into array
                    $fetchResult = mysqli_fetch_array($dataQuery);

                    if ($fetchResult > 0) {
                        //add data into array
                        $send = $sendEmail->verifyRegister($fetchResult["userName"]);
                        if ($send) {
                            //add data into array
                            $jsonData[] = array("Error" => FALSE, "username" => $fetchResult["userName"],
                                "email" => $fetchResult["userEmail"], "created_at" => $fetchResult["created_at"],
                                "activate" => $fetchResult["is_activated"],
                                "email_msg" => "verification code sent");

                            //register successful so redirect to verification page
                            $verify = "verify.html?&username=" . $jsonData[0]['username'] . "";
                            header("Location: $verify");
                        } else {
                            $jsonData[] = array("Error" => TRUE, "email_failed" => "verification code failed to send");
                        }
                    }
                } else {
                    $jsonData[] = array("Error" => TRUE, "error_msg" => "Password match failed");
                }
            } else {
                $jsonData[] = array("Error" => TRUE, "error_msg" => "Invalid email");
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