<?php

    session_start();
    //include('/home/users/web/b2283/ipg.stinttrackercom/home/functions.php');
    include('/home/users/web/b2283/ipg.stinttrackercom/home/db_files/connection.php');
    $error_message = "";
    $drill_name = "";
    $post_message = "";

    if($_SERVER['REQUEST_METHOD'] == "GET" && !isset($_GET['drill_id']))
    {
        $error_message = "Error - no drill selected. Please try again.";
    }
    elseif($_SERVER['REQUEST_METHOD'] == "GET" && !is_numeric($_GET['drill_id']))
    {
        $error_message = "Invalid drill ID. Please try again.";
    }
    elseif($_SERVER['REQUEST_METHOD'] == "GET")
    {
        $published = 0;
        $drill_id = mysqli_real_escape_string($conn, $_GET['drill_id']);
        $sql = 'SELECT * FROM drills WHERE drill_id = ' . $drill_id . ' LIMIT 1;';
        $result = mysqli_query($conn, $sql);
        if($result)
        {
            $row = mysqli_fetch_assoc($result);
            $published = $row['published'];
        }
        if(!$published)
        {
            $error_message = "That drill could not be found.";
        }
    }
    elseif($_SERVER['REQUEST_METHOD'] == "POST")
    {
        if(empty($_SESSION['username']))
        {
            $error_message = "No user logged in. Please log in to record results.";
        }
        else
        {
            $published = 0;
            $drill_id = $_POST['drill_id'];
            $sql = "SELECT id FROM users WHERE username = '" . $_SESSION['username'] . "';";
            $result = mysqli_query($conn, $sql);
            $user_row = mysqli_fetch_array($result);
            $user_id = $user_row['id'];
            $sql = 'SELECT * FROM drills WHERE drill_id = ' . $drill_id . ' LIMIT 1;';
            $result = mysqli_query($conn, $sql);
            if($result)
            {
                $row = mysqli_fetch_assoc($result);
                $published = $row['published'];
                
                if($row['out_of'] && $row['pass_fail'])
                {
                    if(intval($_POST['score']) >= intval($row['out_of_pass']))
                    {
                        $pass = 1;
                        $post_message = "Result recorded, and you passed!";
                    }
                    else
                    {
                        $pass = 0;
                        $post_message = "Result recorded. This was not a passing score, but keep trying!";
                    }
                    $sql = "INSERT INTO `drill_results` (`id`, `user_id`, `drill_id`, `pass`, `score`, `timestamp`) VALUES (NULL, '" . $user_id . "', '" . $drill_id . "', '" . $pass . "', '" . $_POST['score'] . "', current_timestamp())";
                    $insert = mysqli_query($conn, $sql);
                    if(!$insert)
                    {
                        $post_message = "";
                        $error_message = "An error occurred while trying to record your result. Please try again.";
                    }
                }
                elseif($row['score'])
                {
                    $sql = "INSERT INTO `drill_results` (`id`, `user_id`, `drill_id`, `pass`, `score`, `timestamp`) VALUES (NULL, '" . $user_id . "', '" . $drill_id . "', NULL, '" . $_POST['score'] . "', current_timestamp())";
                    $insert = mysqli_query($conn, $sql);
                    if($insert)
                    {
                        $post_message = "Result recorded. Good job!";
                    }
                    else
                    {
                        $post_message = "";
                        $error_message = "An error occurred while recording that result. Please try again.";
                    }                    
                }
                elseif($row['pass_fail'])
                {
                    if($_POST['pass'] == "yes")
                    {
                        $pass = 1;
                    }
                    elseif($_POST['pass'] == "no")
                    {
                        $pass = 0;
                    }
                    else
                    {
                        $error_message = "Encountered a post problem while recording your result. Pleast try again.";
                    }
                    if(empty($error_message))
                    {
                        $sql = "INSERT INTO `drill_results` (`id`, `user_id`, `drill_id`, `pass`, `score`, `timestamp`) VALUES (NULL, '" . $user_id . "', '" . $drill_id . "', '" . $pass . "', NULL, current_timestamp())";
                        $insert = mysqli_query($conn, $sql);
                        if($insert)
                        {
                            $post_message = "Result recorded. You passed!";
                        }
                        else
                        {
                            $post_message = "";
                            $error_message = "Encountered an error while recording that result. Please try again.";
                        }                    
                    }
                }
            }
            if(!$published)
            {
                $error_message = "That drill could not be found.";
            }
        }
    }
?>

<!DOCTYPE html>
<html>
    <head>
        <?php if(empty($error_message)): ?>
            <title>Drill #<?php echo $row['drill_id'] ?> - Pool Practice Tracker</title>
        <?php else: ?>
            <title>Pool Practice Tracker</title>
        <?php endif; ?>
        <link rel="stylesheet" href="/home/styles/general.css">
        <link rel="stylesheet" href="/home/styles/header.css">
        <link rel="stylesheet" type="text/css" href="/home/styles/user-handling.css">
        <link rel="stylesheet" type="text/css" href="/home/styles/drills.css">
    </head>
    <body style="padding-bottom: 40px;">

        <?php include('/home/users/web/b2283/ipg.stinttrackercom/home/temps/header.php'); ?>

        <?php if(!empty($error_message)): ?>
            <div class="ueser-form-error" style="width: 600px; margin-top: 100px;">
                <?php echo $error_message; ?>
            </div>
            <?php die(); ?>
        <?php endif; ?>

        <main class="main-section">
            <div class="page-subtitle">
                Drill #<?php echo $row['drill_id']; ?> - <?php echo $row['name']; ?>
            </div>
            <div class="text-grid-three-by-one">
                <div class="left-section"> </div>
                <div class="middle-section">
                    <div class="left-justified-paragraph">
                        <?php echo $row['description']; ?>
                    </div>
                </div>
                <div class="right-section"></div>
            </div>

            <?php if(!empty($_SESSION['username'])): ?>
                <div class="user-heading">
                    <h2>Record your practice result!</h2>
                </div>
                <form action="drill_details.php" class="user-form" method="post">
                    <?php if(!empty($post_message)): ?>
                        <div style="width: 92%; margin: 0px auto; padding: 10px; border: 1px solid rgb(117, 199, 199); color: rgb(117, 199, 199); background: rgb(225, 255, 255); border-radius: 5px; text-align: left;">
                            <p><?php echo $post_message; ?></p>
                        </div>
                        <?php endif; ?>
                    <div class="input-group">
                        <label for="drill_id">Drill #</label>
                        <select name="drill_id" id="drill_id" readonly="true">
                            <option value="<?php echo $row['drill_id']; ?>"><?php echo $row['drill_id']; ?></option>
                        </select>
                    </div>
                    <!--an "out of" means that the drill has a score out of number, such as scoring 7 out of 10 -->
                    <?php if($row['out_of']): ?>
                        <div class="input-group">
                            <label for="score">Score:</label>
                            <select name="score" id="score" style="font-size: 16px">
                                <?php for($i = 0; $i <= $row['out_of_num']; $i++): ?>
                                    <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                                <?php endfor; ?>
                            </select>
                            out of <?php echo $row['out_of_num']; ?>
                        </div>
                    <?php elseif($row['score']): ?>
                        <div class="input-group">
                            <label for="score">Score:</label>
                            <input type="number" id="score" name="score" min="0" style="font-size: 16px;">
                        </div>
                    <?php elseif($row['pass_fail']): ?>
                        <div class="input-group">
                            <label for="pass">Did you pass the drill?</label>
                            <select name="pass" id="pass" style="font-size: 16px;">
                                <option value="no">No</option>
                                <option value="yes">Yes</option>
                            </select>
                        </div>
                    <?php endif; ?>
                    <div class="input-group">
                        <button id="button" type="submit" value="submit" name="submit" class="user-form-btn" style="cursor: pointer;">Submit Result</button>
                    </div>
                </form>
            <?php endif; ?>
            
            <?php if($row['has_table_diagram']): ?>
                <div>
                    <a href="/home/drills/diagrams/<?php echo $row['drill_id']; ?>.png">
                        <img src="/home/drills/diagrams/<?php echo $row['drill_id']; ?>.png" class="centered_table_diagram">
                    </a>
                </div>
                <div class="text-grid-three-by-one">
                    <div class="left-section"></div>
                    <div class="middle-section" style="margin-top: 0px;">
                        Click the image to enlarge.
                    </div>
                    <div class="right-section"></div>
                </div>
            <?php endif; ?>

            <?php if($row['has_video']): ?>
                <div class="centered-youtube-box">
                    <iframe
                        width="560" 
                        height="315" src="https://www.youtube.com/embed/<?php echo $row['youtube_video_code']; ?>"
                        title="YouTube video player"
                        frameborder="0"
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                        allowfullscreen>
                    </iframe>
                </div>
                <div class="text-grid-three-by-one">
                    <div class="left-section"></div>
                    <div class="middle-section" style="margin-top: 0px;">
                        Demonstration of drill #<?php echo $row['drill_id']; ?> by Cellar Cue Sports
                    </div>
                    <div class="right-section"></div>
                </div>
            <?php endif; ?>

            <?php if($row['has_creator_video']): ?>
                <div class="centered-youtube-box">
                    <iframe
                        width="560" 
                        height="315" src="https://www.youtube.com/embed/<?php echo $row['creator_video_youtube_video_code']; ?>"
                        title="YouTube video player"
                        frameborder="0"
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                        allowfullscreen>
                    </iframe>
                </div>
                <div class="text-grid-three-by-one">
                    <div class="left-section"></div>
                    <div class="middle-section" style="margin-top: 0px;">
                        <?php echo $row['creator_video_description']; ?>
                    </div>
                    <div class="right-section"></div>
                </div>
            <?php endif; ?>
        </main>

        <?php include('/home/users/web/b2283/ipg.stinttrackercom/home/temps/footer.php'); ?>

    </body>
</html>
