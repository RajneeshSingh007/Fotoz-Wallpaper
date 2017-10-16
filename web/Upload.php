<?php
    /**
     * Created by IntelliJ IDEA.
     * User: Coolalien
     * Date: 11-Oct-17
     * Time: 11:53 PM
     */


    // Start the session
    session_start();

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
    $fileUpload = array();

    // getting server ip address
    $server_ip = gethostbyname(gethostname());

    if (isset($_FILES['file']['name'])) {

        $file = $_FILES['file']['name'];

        $target_path = "image/";

        $target_path = $target_path . basename($file);

        // final file url that is being uploaded
        $file_upload_url = 'http://localhost/wtl/web/' . $target_path;

        if (!empty($file)) {
            $fname_arr = explode('.', $file);
            $fileext = $fname_arr[count($fname_arr) - 1];
            $ext_arr = array('jpeg', 'png', 'jpg');
            try {

                if (in_array($fileext, $ext_arr)) {
                    // Throws exception incase file is not being moved
                    if (!move_uploaded_file($_FILES['file']['tmp_name'], $target_path)) {
                        // failed to move file
                        $fileUpload[] = array("Error" => TRUE, "Error_msg" => "Could not move the file, Please check file");
                    }

                    $userName = $_SESSION['user'];

                    //query user's detail
                    $dataQuery = $connect->query("SELECT * FROM user WHERE userName='$userName'");

                    $fetchResult = mysqli_fetch_array($dataQuery);

                    $fileArray['name'][] = $file;
                    $fileArray['filepath'][] = $file_upload_url;

                    if ($fetchResult > 0) {
                        $dbFile = $fetchResult["wallPath"];
                        //separate
                        $names = implode(',', $fileArray['name']);
                        $path = implode(',', $fileArray['filepath']);

                        if ($dbFile == $file_upload_url) {
                            $fileUpload[] = array("Error" => TRUE, "Error_msg" => "File already exist");
                        } else {
                            $sqlUpdate = "UPDATE user SET wallName='$names', wallPath = '$path' WHERE userName ='$userName'";
                            //query
                            $queryAgain = $connect->query($sqlUpdate);

                            if ($sqlUpdate) {
                                // File successfully uploaded
                                $fileUpload[] = array("Error" => FALSE, "username" => $userName, "file_path" => $file_upload_url, "success_message" => "Stored successfully");
                                //redirect to dashboard or home according to session store
                                $home = "index.php";
                                header("Location: $home");
                            } else {
                                // failed to save data into db
                                $fileUpload[] = array("Error" => TRUE, "Error_msg" => "Failed to save data into db");
                            }
                        }
                    }

                } else {
                    $fileUpload[] = array("Error" => TRUE, "Error_msg" => "Sorry, only JPG, JPEG & PNG files are allowed.");
                }

            } catch (Exception $e) {
                // Exception occurred. Make Error flag true
                $fileUpload[] = array("Error" => TRUE, "Error_msg" => $e->getMessage());
            }
        } else {
            $fileUpload[] = array("Error" => TRUE, "Error_msg" => "Empty file location");
        }

    } else {
        $fileUpload[] = array("Error" => TRUE, "Error_msg" => "Required parameters is missing");
    }

    //encode into json and print
    echo json_encode($fileUpload, JSON_PRETTY_PRINT);

    //close db connection
    $connect->close();
?>