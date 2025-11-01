<?php
    $dbhost="stinttrackercom.ipagemysql.com";
    $dbuser="pptadm1234";
    $dbpass="Eddie&Ella12";
    if(!$conn = mysqli_connect($dbhost, $dbuser, $dbpass)) {
        die(mysqli_connect_error());
    }
    mysqli_select_db($conn, 'poolpracticetracker');
?>