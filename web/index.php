<?php

    /**
     * Created by IntelliJ IDEA.
     * User: Coolalien
     * Date: 08-Oct-17
     * Time: 7:14 PM
     */

    session_start();

    //check if user login session
    if (isset($_SESSION['user'])) {
        // true so launch userdashboard web
        header("Location: userdashboard.html");
    } else {
        // false so launch home homepage
        header("Location: home.html");
    }
?>