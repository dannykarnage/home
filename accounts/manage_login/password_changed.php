<?php

    session_start();
    //include('/home/users/web/b2283/ipg.stinttrackercom/home/functions.php');

    if(isset($_SESSION['username']))
    {
        unset($_SESSION['username']);
    }

    if(isset($_SESSION['username']))
    {
        die('something went wrong');
    }
?>

<!DOCTYPE html>

<html>
    <head>
        <title>
            Password Changed - Pool Practice Tracker
        </title>
        <link rel="stylesheet" href="/home/styles/general.css">
        <link rel="stylesheet" href="/home/styles/header.css">
    </head>
    <body>
        
        <header>
            <?php include('/home/users/web/b2283/ipg.stinttrackercom/home/temps/header.php') ?>
        </header>

        <main>
            <div style="margin-top: 66px"></div>
            <div class="text-grid-three-by-one">
                <div class="left-section"> </div>
                <div class="middle-section">
                    <div class="left-justified-paragraph">
                        <p>
                            <h2>Password changed</h2>
                        </p>
                        <p>
                            Your password has been changed and you have been logged out. Please 
                            log in again to use Pool Practice Tracker!
                        </p>
                    </div>
                </div>
                <div class="right-section"> </div>
            </div>
        </main>

        <footer>

            <?php include('/home/users/web/b2283/ipg.stinttrackercom/home/temps/footer.php'); ?>

        </footer>

    </body>
</html>