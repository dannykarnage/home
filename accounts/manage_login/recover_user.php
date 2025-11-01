<?php

    session_start();

    //include('/home/users/web/b2283/ipg.stinttrackercom/home/functions.php');
    include('/home/users/web/b2283/ipg.stinttrackercom/home/db_files/connection.php');
    $error_message = "";

    //determine if a user is already logged in and, if so, redirect to the homepage
    $user_already_logged_in = !empty($_SESSION['username']);
    if($user_already_logged_in)
    {
        $error_message = "A user is already logged in. To change user, please logout first.";
    }
    elseif($_SERVER['REQUEST_METHOD'] == "POST")
    {
        $email = trim($_POST['email']);
        $email = mysqli_real_escape_string($conn, $email);
        
        if(!empty($email))
        {
            $sql = "SELECT `username` FROM `users` WHERE `users`.`email` = '$email';";
            $result = mysqli_query($conn, $sql);

            if ($result)
            {
                $row = mysqli_fetch_assoc($result);
                $username = $row['username'];
                
                if (!empty($username))
                {

                    //send email
                    $subject = "Username request";
                    include('/home/users/web/b2283/ipg.stinttrackercom/home/accounts/manage_login/recover_username_email.php');
                    $headers = "From: donotreply@poolpracticetracker.com \r\n";
                    $headers .= "MIME-Version: 1.0" . "\r\n";
                    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
                    
                    $success = mail($email,$subject,$email_message,$headers);

                    if (!$success)
                    {
                        $error_message = "Unable to process your request. Please try again later.";
                    }
                }

                header("Location: /home/accounts/manage_login/username_requested.php");
            }
            else
            {
                $error_message = "A problem occurred while trying to locate that email address. Please try again.";
            }
        }
        else
        {
            $error_message = "Email address is required.";
        }
    }

?>

<!DOCTYPE html>

<html>
    <head>
        <title>
            Recover Username - Pool Practice Tracker
        </title>
        <link rel="stylesheet" type="text/css" href="/home/styles/general.css">
        <link rel="stylesheet" type="text/css" href="/home/styles/header.css">
        <link rel="stylesheet" type="text/css" href="/home/styles/user-handling.css">
    </head>
    <body>
        
        
        <?php include('/home/users/web/b2283/ipg.stinttrackercom/home/temps/header.php'); ?>

        <main class="main-section">
            <div class="user-heading">
                <h2>Recover Username</h2>
            </div>
            <form class="user-form" action="/home/accounts/manage_login/recover_user.php " method="post">
                <?php if(!empty($error_message)): ?>
                    <div style="width: 92%; margin: 0px auto; padding: 10px; border: 1px solid #a94442; color: #a94442; background: #f2dede; border-radius: 5px; text-align: left;">
                        <p><?php echo $error_message; ?></p>
                    </div>
                <?php endif; ?>
                <div class="input-group"> <!--this isn't an input, but the input-group style works fine -->
                    Enter your email address below. If we have an account with the submitted email, we will
                    send you an email containing your username.
                </div>
                <div><br></div>
                <div class="input-group">
                    <label for="">Email address</label>
                    <input id="text" type="text" name="email">
                </div>
                <div class="input-group">
                    <button id="button" type="submit" value="submit" name="submit" class="user-form-btn" style="cursor: pointer;">Submit</button>
                </div>
            </form>
        </main>

        <?php include('/home/users/web/b2283/ipg.stinttrackercom/home/temps/footer.php'); ?>

    </body>
</html>
