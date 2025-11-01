<?php

    //include('/home/users/web/b2283/ipg.stinttrackercom/home/functions.php');
    $success = false;

    session_start();

    $errors = array();
    $username = "";
    $email = "";
    $password_1 = "";
    $password_2 = "";

    //determine if a user is already logged in and, if so, redirect to the homepage
    $user_already_logged_in = !empty($_SESSION['username']);
    if($user_already_logged_in)
    {
        array_push($errors, "A user is already logged in. To change user, please logout first.");
    }
    elseif($_SERVER['REQUEST_METHOD'] == "POST")
    {
        if(isset($_POST['submit']))
        {            
            include("/home/users/web/b2283/ipg.stinttrackercom/home/db_files/connection.php");
            $username = mysqli_real_escape_string($conn, $_POST['username']);
            $email = mysqli_real_escape_string($conn, $_POST['email']);
            $password_1 = mysqli_real_escape_string($conn, $_POST['password_1']);
            $password_2 = mysqli_real_escape_string($conn, $_POST['password_2']);
            
            $username = trim($username);
            $email = trim($email);
            $password_1 = trim($password_1);
            $password_2 = trim($password_2);

            if(empty($username))
            {
                array_push($errors, "Username is required.");
            }
            if(empty($email))
            {
                array_push($errors, "Email is required");
            }
            else
            {
                if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
                    array_push($errors, 'Email must be a valid email address.');
                }    
            }
            if(empty($password_1))
            {
                array_push($errors, "Password is required");
            }
            if($password_1 != $password_2)
            {
                array_push($errors, "Passwords do not match");  
            }
            if(!preg_match('/^[a-zA-Z0-9_.-]*$/', $username))
            {
                array_push($errors, "Username must only contain letters, numbers, underscore (_), period (.), or dash (-).");
            }
            if(!preg_match('/^[a-zA-Z]*$/', $username[0]))
            {
                array_push($errors, "Username must start with a letter.");
            }
            if(strlen($username) < 6 || strlen($username > 32))
            {
                array_push($errors, "Username must be between 6 and 32 characters in length.");
            }
            if(strlen($password_1) < 8 || strlen($password_1) > 32)
            {
                array_push($errors, "Password must be between 8 and 32 characters in length.");
            }

            if(count($errors) == 0)
            {
                if(!preg_match('/^[a-zA-Z0-9._!@#$%^&*()-]*$/', $password_1) || preg_match('/^[\/]*$/', $password_1))
                {
                    array_push($errors, "Password must only contain letters, numbers, or symbols ._!@#$%^&*()-");
                }
            }

            if (count($errors) == 0)
            {
                $sql = "SELECT * FROM `users` WHERE `users`.`username` LIKE '$username'";
                $result = mysqli_query($conn, $sql);
                if($result)
                {
                    if(mysqli_num_rows($result) > 0)
                    {
                        array_push($errors, "User already exists");
                    }
                }
                else
                {
                    array_push($errors, "Trouble with registering username. Please try again later or use the 'Contact Us' form to contact a site admin.");
                }
            }

            if (count($errors) == 0)
            {
                $sql = "SELECT * FROM `users` WHERE `users`.`email` LIKE '$email'";
                $result = mysqli_query($conn, $sql);
                if($result)
                {
                    if(mysqli_num_rows($result) > 0)
                    {
                        array_push($errors, "A user with that email address already exists.");
                    }
                }
                else
                {
                    array_push($errors, "Trouble with registering email address. Please try again later or use the 'Contact Us' form to contact a site admin.");
                }
            }

            //were there any errors?
            if(count($errors) == 0) {
                $password = md5($password_1); //encrypt password before storing in database
                //echo '<br><br><br><br><br><br>' . $password_1 . '     ' . $password;  //this liine is for troubleshooting - comment out for publication
                $vkey = md5(time().$username);
                $sql = "INSERT INTO users (`username`, `email`, `password`, `vkey`) VALUES ('$username', '$email', '$password', '$vkey');";
                //echo $sql . '<br>';  //this line is for troubleshooting - comment out for pulication
                $result = mysqli_query($conn, $sql);

                if($result)
                {
                    //send email
                    $subject = "Email verification";
                    //$email_message is defined in the verification-email.php file
                    include('/home/users/web/b2283/ipg.stinttrackercom/home/accounts/verification-email.php');
                    $headers = "From: donotreply@poolpracticetracker.com \r\n";
                    $headers .= "MIME-Version: 1.0" . "\r\n";
                    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

                    $success = mail($email,$subject,$email_message,$headers);
                }
                else
                {
                    array_push($errors, "Trouble connecting with database. Please try again later or use the 'Contact Us' form to contact a site admin.");
                }
            }
        }
    }

    if($success)
    {
        header("Location: /home/accounts/thankyou.php");
    }

?>

<!DOCTYPE html>
<html>
    <head>
        <title>Registration - Pool Practice Tracker</title>
        <link rel="stylesheet" type="text/css" href="/home/styles/general.css">
        <link rel="stylesheet" type="text/css" href="/home/styles/header.css">
        <link rel="stylesheet" type="text/css" href="/home/styles/user-handling.css">
        <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    </head>
    <body>
        
        <?php include('/home/users/web/b2283/ipg.stinttrackercom/home/temps/header.php'); ?>

        <main class="main-section">
            <div class="user-heading">
                <h2>Register</h2>
            </div>
            <form class="user-form" action="/home/accounts/register.php " method="post">
                <?php if(count($errors) > 0): ?>
                <div style="width: 92%; margin: 0px auto; padding: 10px; border: 1px solid #a94442; color: #a94442; background: #f2dede; border-radius: 5px; text-align: left;">
                    <?php foreach ($errors as $error): ?>
                        <p><?php echo $error; ?></p>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
                <div class="input-group">
                    <label for="">Username</label>
                    <input id="text" type="text" name="username" value="<?php echo htmlspecialchars($username); ?>">
                </div>
                <div class="input-group">
                    <label for="">Email address</label>
                    <input id="text" type="text" name="email" value="<?php echo htmlspecialchars($email); ?>">
                </div>
                <div class="input-group">
                    <label for="">Password</label>
                    <input id="text" type="password" name="password_1" value="<?php echo htmlspecialchars($password_1); ?>">
                </div>
                <div class="input-group">
                    <label for="">Confirm password</label>
                    <input id="text" type="password" name="password_2" value="<?php echo htmlspecialchars($password_2); ?>">
                </div>                
                <div class="g-recaptcha" data-sitekey="6LdhASUjAAAAAM8x8JpWFkP0Oe-JbiYr6GeUyVm4"></div>
                <div class="input-group">
                    <button id="button" type="submit" value="submit" name="submit" class="user-form-btn" style="cursor: pointer">Register</button>
                </div>
                <p>
                    Already a member? <a href="/home/accounts/login.php">Login!</a>
                </p>
            </form>
        </main>

        <?php include('/home/users/web/b2283/ipg.stinttrackercom/home/temps/footer.php'); ?>

    </body>
</html>