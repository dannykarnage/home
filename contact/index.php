<?php

    session_start();

    $success = false;
    $errors = array();
    $name = "";
    $email = "";
    $subject = "";
    $message = "";

    if($_SERVER['REQUEST_METHOD'] == "POST")
    {
        if(isset($_POST['submit']))
        {
            include("/home/users/web/b2283/ipg.stinttrackercom/home/db_files/connection.php");
            $name = mysqli_real_escape_string($conn, $_POST['name']);
            $email = mysqli_real_escape_string($conn, $_POST['email']);
            $subject = mysqli_real_escape_string($conn, $_POST['subject']);
            $message = mysqli_real_escape_string($conn, $_POST['message']);
            
            $name = trim($name);
            $email = trim($email);
            $subject = trim($subject);
            $message = trim($message);

            $name = htmlspecialchars($name);
            $email = htmlspecialchars($email);
            $subject = htmlspecialchars($subject);
            $message = htmlspecialchars($message);

            $name = stripslashes($name);
            $email = stripslashes($email);
            $subject = stripslashes($subject);
            $message = stripslashes($message);

            if(empty($name))
            {
                array_push($errors, "Name is required.");
            }
            if(empty($email))
            {
                array_push($errors, "Email address is required");
            }
            if(empty($subject))
            {
                array_push($errors, "Please include a subject");
            }
            if(empty($message))
            {
                array_push($errors, "You must include a message for your email");
            }

            if (count($errors) == 0)
            {
                $full_message = "You have a new email.\r\n";
                $full_message .= "\r\n";
                $full_message .= "Name: " .$name . "\r\n";
                $full_message .= "Email: " .$email . "\r\n";
                $full_message .= "\r\n";
                $full_message .= $message;
                //send email
                $headers = "From: contactform@poolpracticetracker.com \r\n";
                $headers .= "MIME-Version: 1.0" . "\r\n";
                $headers .= "Content-type:text/plain;charset=UTF-8" . "\r\n";
                $success = mail("contactform@poolpracticetracker.com",$subject,$full_message,$headers);
            }
        }
    }

    if($success)
    {
        header("Location: /home/contact/thankyou.php");
    }

?>

<html>
    <head>
<script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-0531879237494766" crossorigin="anonymous"></script>
        <title>
            Contact Us - Pool Practice Tracker
        </title>
        <link rel="stylesheet" type="text/css" href="/home/styles/general.css">
        <link rel="stylesheet" type="text/css" href="/home/styles/header.css">
        <link rel="stylesheet" type="text/css" href="/home/styles/user-handling.css">
        <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    </head>
    <body>
        
        <?php include('/home/users/web/b2283/ipg.stinttrackercom/home/temps/header.php'); ?>

        <main class="main-section">

            <div class="user-heading">
                <h2>Contact Us</h2>
            </div>
            <?php if(count($errors) > 0): ?>
                <div style="width: 92%; margin: 0px auto; padding: 10px; border: 1px solid #a94442; color: #a94442; background: #f2dede; border-radius: 5px; text-align: left;">
                    <?php foreach ($errors as $error): ?>
                        <p><?php echo $error; ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            <form class="user-form" action="/home/contact/index.php " method="post">
                <div class="input-group">
                    <label for="">Your name: </label>
                    <input id="name" type="text" name="name">
                </div>
                <div class="input-group">
                    <label for="">Your email address: </label>
                    <input id="email" type="text" name="email">
                </div>
                <div class="input-group">
                    <label for="">Subject: </label>
                    <input id="subject" type="text" name="subject">
                </div>
                <div class="input-group">
                    <label for="">Message: </label>
                    <textarea id="message" name="message" cols="55" rows="15"></textarea>
                </div>
                <div class="g-recaptcha" data-sitekey="6LdhASUjAAAAAM8x8JpWFkP0Oe-JbiYr6GeUyVm4"></div>
                <div class="input-group">
                    <button id="button" type="submit" value="send" name="send" class="user-form-btn" style="cursor: pointer;">Send Email</button>
                </div>
            </form>

        </main>

    </body>
</html>