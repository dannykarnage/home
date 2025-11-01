<?php

    session_start();
    //include('/home/users/web/b2283/ipg.stinttrackercom/home/functions.php');
    include('/home/users/web/b2283/ipg.stinttrackercom/home/db_files/connection.php');
    $error_message = "";
    $outcome = "";

    if(isset($_GET['pkey']))
    {
        $pkey = $_GET['pkey'];
        $sql = "SELECT `username` FROM `users` WHERE `verified` = 1 AND `pkey` = '$pkey';";
        $result = mysqli_query($conn, $sql);
        if (!$result)
        {
            $error_message = "Database issue accessing account. Please try again later.";
        }
        elseif(mysqli_num_rows($result) != 1)
        {
            $error_message = "Could not locate a matching password reset request. Please try resetting your password again.";
        }
        else
        {
            $row = mysqli_fetch_assoc($result);
            $username = $row['username'];
            $sql = "SELECT TIMESTAMPDIFF(SECOND, `password_reset_request_timestamp`, CURRENT_TIMESTAMP) AS 'difference' FROM `users` WHERE `pkey` = '$pkey';";
            $result = mysqli_query($conn, $sql);
            if (!result)
            {
                $error_message = "Database issue accessing time verification. Please try again later or use the Contact Us link above to report the issue.";
            }
            else
            {
                $row = mysqli_fetch_assoc($result);
                $timediff = (int) $row['difference'];
                if($timediff > 86400)
                {
                    $error_message = "That password reset request has expired. Please use attempt to reset your password again.";
                }
                else
                {
                    $sql = "UPDATE `users` SET `password_reset_required` = 1 WHERE `pkey` = '$pkey';";
                    $result = mysqli_query($conn, $sql);
                    if(!result)
                    {
                        $error_message = "An error occurred with your request. Please try to reset your password again.";
                    }
                }
            }
        }

    }
    elseif(isset(($_POST['submit'])))
    {
        if(isset($_SESSION['username']))
        {
            $username = $_SESSION['username'];
            $current_password = md5(trim(mysqli_real_escape_string($conn, $_POST['current_password'])));
            $sql = "SELECT `password` FROM `users` WHERE `username` = '$username';";
            $result = mysqli_query($conn, $sql);
            if($result)
            {
                //echo "<script>alert('got here.');</script>";
                $row = mysqli_fetch_assoc($result);

                //$temp = $row['password'] !== $current_password;
                //echo "<script>alert('$temp');</script>";
                
                if($row['password'] !== $current_password)
                {
                    $error_message = "Current password is incorrect. Please try again.";
                }
            }
        }
        else
        {
            $pkey = $_POST['pkey'];
            $sql = "SELECT `username` FROM `users` WHERE `pkey` = '$pkey';";
            $result = mysqli_query($conn, $sql);
            if($result)
            {
                $row = mysqli_fetch_assoc($result);
                $username = $row['username'];
            }
            else
            {
                $error_message = "An error occurred while resetting the password. Please try again or contact a site admin.";
            }
        }

        if(empty($error_message))
        {
            $password_1 = trim(mysqli_real_escape_string($conn, $_POST['new_password']));
            $password_2 = trim(mysqli_real_escape_string($conn, $_POST['confirm_password']));
            if($password_1 !== $password_2)
            {
                $error_message = "Passwords do not match. Please try again.";
            }
            elseif(strlen($password_1) < 8 || strlen($password_1) > 32)
            {
                $error_message = "Password must be between 8 and 32 characters in length.";
            }
            elseif(!preg_match('/^[a-zA-Z0-9._!@#$%^&*()-]*$/', $password_1) || preg_match('/^[\/]*$/', $password_1))
            {
                $error_message = "Password must only contain letters, numbers, or symbols ._!@#$%^&*()-";
            }
            else
            {
                $new_password = md5($password_1);
                $sql = "UPDATE `users` SET `password` = '$new_password', `pkey` = NULL, `password_reset_required` = 0 WHERE `username` = '$username';";
                $result = mysqli_query($conn, $sql);
                if($result)
                {
                    header("Location: /home/accounts/manage_login/password_changed.php");
                }
                else
                {
                    $error_message = "Something went wrong while changing the password. Please try again.";
                }
            }
        }

    }
?>

<!DOCTYPE html>
<html>
    <head>
        <title>
            Reset Password - Pool Practice Tracker
        </title>
        <link rel="stylesheet" type="text/css" href="/home/styles/general.css">
        <link rel="stylesheet" type="text/css" href="/home/styles/header.css">
        <link rel="stylesheet" type="text/css" href="/home/styles/user-handling.css">

    </head>
    <body>
        
        <?php include('/home/users/web/b2283/ipg.stinttrackercom/home/temps/header.php'); ?>

        <main class="main-section">
            <div class="user-heading">
                <h2>
                    <?php if(!isset($_GET['pkey'])): ?>
                        Change Password
                    <?php else: ?>
                        Reset Password
                    <?php endif; ?>
                </h2>
            </div>
            <form class="user-form" action="/home/accounts/manage_login/change_password.php " method="post">
                <?php if(!empty($error_message)): ?>
                    <div style="width: 92%; margin: 0px auto; padding: 10px; border: 1px solid #a94442; color: #a94442; background: #f2dede; border-radius: 5px; text-align: left;">
                        <p><?php echo $error_message; ?></p>
                    </div>
                <?php endif; ?>
                <?php if(!(isset($_GET['pkey']) && !empty($error_message))): ?>
                    <?php if(!isset($_GET['pkey']) && isset($_SESSION['username'])): ?>
                        <div class="input-group">
                            <label for="">Current Password</label>
                            <input id="text" type="password" name="current_password">
                        </div>
                    <?php else: ?>
                        <div>
                            <input id="pkey" type="hidden" name="pkey" value="<?php echo $pkey; ?>">
                        </div>
                    <?php endif; ?>
                    <div class="input-group">
                        <label for="">New Password</label>
                        <input id="text" type="password" name="new_password">
                    </div>
                    <div class="input-group">
                        <label for="">Current Password</label>
                        <input id="text" type="password" name="confirm_password">
                    </div>
                    <div class="input-group">
                        <button id="button" type="submit" value="change_password" name="submit" class="user-form-btn" style="cursor: pointer;">Submit</button>
                    </div>
                <?php endif; ?>
            </form>
        </main>

        <?php include('/home/users/web/b2283/ipg.stinttrackercom/home/temps/footer.php'); ?>

    </body>
</html>