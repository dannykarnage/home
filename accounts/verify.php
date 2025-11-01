<?php

    if(isset($_GET['vkey']))
    {
        $outcome = "";
        //include('/home/users/web/b2283/ipg.stinttrackercom/home/functions.php');

        $vkey = $_GET['vkey'];

        include('/home/users/web/b2283/ipg.stinttrackercom/home/db_files/connection.php');

        $sql = "SELECT verified, vkey FROM users WHERE verified = 0 AND vkey = '$vkey' LIMIT 1;";
        $result = mysqli_query($conn, $sql);

        if(mysqli_num_rows($result) != 1)
        {
            $sql = "SELECT verified, vkey FROM users WHERE verified = 1 AND vkey = '$vkey' LIMIT 1;";
            $result = mysqli_query($conn, $sql);
            if(mysqli_num_rows($result) == 1)
            {
                $outcome = "This account has already been verified.";
            }
            else
            {
                $outcome = "Could not locate that account. Please try again.";
            }
        }
        else
        {
            //validate the email;
            $sql = "UPDATE users SET verified = 1 WHERE vkey = '$vkey' LIMIT 1;"; 
            $result = mysqli_query($conn, $sql);
            // $sql = "SELECT verified, vkey FROM accounts WHERE verified = 0 AND vkey = '$vkey' LIMIT 1;";
            // $result = mysqli_query($conn, $sql);

            if($result)
            {
                $outcome = "Your account has been verified. Click the \"Login\" button above to log in to your account!";
            }
            else
            {
                $outcome = "DB error: " . mysqli_error($conn);
            }
        }
    }
    else
    {
        $outcome = "Something went wrong.";
        die();
    }

?>

<!DOCTYPE html>
<html>
    <head>
    <head>
        <title>Account Verification - by Cellar Cue Sports</title>
        <link rel="stylesheet" href="/home/styles/general.css">
        <link rel="stylesheet" href="/home/styles/header.css">
    </head>
    </head>
    <body>

        <?php include('/home/users/web/b2283/ipg.stinttrackercom/home/temps/header.php'); ?>

        <main class="main-section">
        <div class="text-grid-three-by-one">
                <div class="left-section"> </div>
                <div class="middle-section">
                    <div class="left-justified-paragraph">
                        <p>
                            <?php echo $outcome; ?>
                        </p>
                    </div>
                </div>
                <div class="right-section"> </div>
            </div>
        </main>

        </main>

        <?php include('/home/users/web/b2283/ipg.stinttrackercom/home/temps/footer.php'); ?>
    </body>
</html>