<?php
    session_start();

    //include('/home/users/web/b2283/ipg.stinttrackercom/home/functions.php');
    include('/home/users/web/b2283/ipg.stinttrackercom/home/db_files/connection.php');
    $login_error = "";
    
    //determine if a user is already logged in and, if so, redirect to the homepage
    $user_already_logged_in = !empty($_SESSION['username']);
    if($user_already_logged_in)
    {
        $login_error = "A user is already logged in. To change user, please logout first.";
    }
    elseif($_SERVER['REQUEST_METHOD'] == "POST")
    {
        $username = trim($_POST['username']);
        $password = trim($_POST['password']);

        if(!empty($username) && !empty($password))
        {
            $username = mysqli_real_escape_string($conn, $username);
            $password = mysqli_real_escape_string($conn, $password);
            $password = md5($password);

            $sql = "SELECT `username`, `password`, `verified`, `password_reset_required` FROM `users` WHERE `users`.`username` = '$username' AND `users`.`verified` = 1 limit 1;";
            $result = mysqli_query($conn, $sql);

            if($result && mysqli_num_rows($result) == 1)
            {
                $user_data = mysqli_fetch_assoc($result);
                if($user_data['password_reset_required'] == 1)
                {
                    $login_error = "The password on that account has been disabled. Please click on the <b>Having Trouble?</b> link below to request your password be reset.";
                }
                elseif($user_data['password'] === $password)
                {
                    $_SESSION['username'] = $user_data['username'];
                    header("Location: /home/index.php");
                    die;
                }
            }
            if(empty($login_error))
            {
                $sql = "SELECT `username`, `password`, `verified` FROM `users` WHERE `users`.`username` = '$username' AND `users`.`verified` = 0 limit 1;";
                $result = mysqli_query($conn, $sql);

                if($result && mysqli_num_rows($result) == 1)
                {
                    $login_error = "That account has not yet been verified. Please click the link in the verification email to verify your account before logging in.";
                }
                else
                {
                    $login_error = "Incorrect password or user not found.";
                }
            }
        }
        else
        {
            $login_error = "Invalid login credentials. Please try again.";
        }
    }
?>

<!DOCTYPE html>
<html>
    <head>
        <title>Login - Pool Practice Tracker</title>
        <link rel="stylesheet" type="text/css" href="/home/styles/general.css">
        <link rel="stylesheet" type="text/css" href="/home/styles/header.css">
        <link rel="stylesheet" type="text/css" href="/home/styles/user-handling.css">
    </head>
    <body>
        
        <?php include('/home/users/web/b2283/ipg.stinttrackercom/home/temps/header.php'); ?>

        <main class="main-section">
            <div class="user-heading">
                <h2>Login</h2>
            </div>
            <form class="user-form" action="/home/accounts/login.php " method="post">
                <?php if(!empty($login_error)): ?>
                    <div style="width: 92%; margin: 0px auto; padding: 10px; border: 1px solid #a94442; color: #a94442; background: #f2dede; border-radius: 5px; text-align: left;">
                        <p><?php echo $login_error; ?></p>
                    </div>
                <?php endif; ?>
                <div class="input-group">
                    <label for="">Username</label>
                    <input id="text" type="text" name="username">
                </div>
                <div class="input-group">
                    <label for="">Password</label>
                    <input id="text" type="password" name="password">
                </div>
                <div class="input-group">
                    <button id="button" type="submit" value="login" name="login" class="user-form-btn" style="cursor: pointer;">Login</button>
                </div>
                <p>
                    New here? <a href="/home/accounts/register.php">Sign up!</a>
                </p>
                <p style="font-size: 12px">
                    Having trouble? <a href="/home/accounts/manage_login/request_password_reset.php">Click here.</a>
                </p>
            </form>
        </main>

        <?php include('/home/users/web/b2283/ipg.stinttrackercom/home/temps/footer.php'); ?>

    </body>
</html>