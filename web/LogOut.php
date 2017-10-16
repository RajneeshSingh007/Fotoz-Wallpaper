<?php

    /**
     * Created by IntelliJ IDEA.
     * User: Coolalien
     * Date: 08-Oct-17
     * Time: 4:41 PM
     */


    session_start();
    //destroy session
    if (session_destroy()) {
        header("Location: index.php"); // Redirecting To Home Page
    }
?>