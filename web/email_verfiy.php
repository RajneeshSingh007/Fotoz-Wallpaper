<?php

/**
 * Created by IntelliJ IDEA.
 * User: Rajneesh
 * Date: 08-Oct-17
 * Time: 1:25 PM
 */
class email_verfiy
{

    // function to send email for verification
    public function verifyRegister($userName){

        //Connection file call
        require_once('Connection.php');

        $connection = new Connection();

        //connection call
        $connect = $connection->Connect();

        //connection check
        if (!$connect) {
            die(mysqli_connect_errno());
        }

        // query row data by username
        $sqlQuery = "SELECT * FROM user WHERE userName='$userName'";

        $fetchRow = $connect->query($sqlQuery);

        //fetch result in array
        $result = mysqli_fetch_array($fetchRow);

        if ($result > 0) {
            $email = $result["userEmail"]; //email got
            $verifyCode = $result["email_verify"]; //email verify code

            $to = $email;
            $subject = "Fotoz Registration"; //subject
            $message = "Thanks for registration in Fotoz social site \n
                        Please, enter verification code in the site to approved your registration\n
                        -------------------------------------
                        verification code : $verifyCode
                        -------------------------------------";

            $header = 'From:fotoz.com' . "\r\n";
            mail($to, $subject, $message, $header);
            return true;
        } else {
            return false;
        }
    }
}

?>