<?php

    session_start();

    //include('/home/users/web/b2283/ipg.stinttrackercom/home/functions.php');
    include('/home/users/web/b2283/ipg.stinttrackercom/home/db_files/connection.php');

    //if the user isn't logged in, redirect to the home page
    if(!isset($_SESSION['username']))
    {
        header('Location: /home/');
    }

    //if the drill number (drill_id) is not set, redirect back to the accounts page
    if(!isset($_GET['drill_num']))
    {
        header('Location: /home/accounts');
    }

    $drill_id = $_GET['drill_num'];

    $sql = "SELECT `id` FROM `users` WHERE `users`.`username` = '" . $_SESSION['username'] . "';";
    $result = mysqli_query($conn, $sql);
    if(!$result)
    {
        //means the username has no user_id. Log the user out
        header('Location: /home/accounts/logout.php');
    }
    $user_id = mysqli_fetch_assoc($result)['id'];

    $sql = "SELECT * FROM `drill_results` WHERE `drill_results`.`user_id` = " . $user_id . " AND `drill_results`.`drill_id` = " . $drill_id . " ORDER BY `drill_results`.`timestamp` ASC;";
    $drill_results = mysqli_query($conn, $sql);
    if(!$drill_results || mysqli_num_rows($drill_results) === 0)
    {
        //means that the query failed. Redirect back to the accounts page.
        header('Location: /home/accounts');
    }

    $sql = "SELECT * FROM `drills` WHERE `drills`.`drill_id` = " . $drill_id . ";";
    $drill_detail_results = mysqli_query($conn, $sql);
    if(!$drill_detail_results || mysqli_num_rows($drill_detail_results) <> 1)
    {
        header('Location: /home/accounts');
    }

    //drill types
    // 1 = pass/fail only
    // 2 = score only
    // 3 = score out of number without pass/fail
    // 4 = score out of number with pass/fail
    $drill_type = 0;
    $data = array(
        "timestamp" => array()
    );
    $drill_details = mysqli_fetch_assoc($drill_detail_results);
    //deterimine the drill type
    if ($drill_details['pass_fail'] && !$drill_details['out_of'])
    {
        $drill_type = 1;
        $data["pass"] = array();
        $i = 0;
        while ($row = mysqli_fetch_assoc($drill_results))
        {
            $data["timestamp"][$i] = $row['timestamp'];
            if ($row['pass'])
            {
                $data["pass"][$i] = "Pass";
            }
            else
            {
                $data["pass"][$i] = "Fail";
            }
            $i++;
        }
    }
    elseif ($drill_details['score'] && !$drill_details['out_of'])
    {
        $drill_type = 2;
        $data["score"] = array();
        $i = 0;
        $max_score = -99999;
        $min_score = 99999;
        while ($row = mysqli_fetch_assoc($drill_results))
        {
            $data["timestamp"][$i] = $row['timestamp'];
            $data["score"][$i] = $row['score'];
            if($data["score"][$i] > $max_score)
            {
                $max_score = $data["score"][$i];
            }
            if($data["score"][$i] < $min_score)
            {
                $min_score = $data["score"][$i];
            }
            $i++;
        }
    }
    elseif ($drill_details['score'] && $drill_details['out_of'] && !$drill_details['pass_fail'])
    {
        $drill_type = 3;
        $data["score"] = array();
        $i = 0;
        while ($row = mysqli_fetch_assoc($drill_results))
        {
            $data["timestamp"][$i] = $row['timestamp'];
            $data["score"][$i] = $row['score'];
            $i++;
        }
    }
    elseif ($drill_details['score'] && $drill_details['out_of'] && $drill_details['pass_fail'])
    {
        $drill_type = 4;
        $data["score"] = array();
        $data["pass"] = array();
        $i = 0;
        while ($row = mysqli_fetch_assoc($drill_results))
        {
            $data["timestamp"][$i] = $row['timestamp'];
            $data["score"][$i] = $row['score'];
            if ($row['pass'])
            {
                $data["pass"][$i] = "Pass";
            }
            else
            {
                $data["pass"][$i] = "Fail";
            }
            $i++;
        }
    }
    else
    {
        header('Location : /home/accounts');
    }
?>
<!DOCTYPE html>

<html>
    <head>
        <title>
            My Account - Pool Practice Tracker
        </title>
        <link rel="stylesheet" type="text/css" href="/home/styles/general.css">
        <link rel="stylesheet" type="text/css" href="/home/styles/header.css">
        <link rel="stylesheet" type="text/css" href="/home/styles/history.css">
    </head>
    <body>

        <?php include('/home/users/web/b2283/ipg.stinttrackercom/home/temps/header.php'); ?>

        <main class="main-section">
            <div class="history-header">
                <h4>History of Drill #<?php echo $drill_id; ?> for <?php echo $_SESSION['username']; ?></h4>
            </div>

            <?php include('/home/users/web/b2283/ipg.stinttrackercom/home/drills/type' . $drill_type . '_drill_html.php'); ?>
        </main>

        <?php include('/home/users/web/b2283/ipg.stinttrackercom/home/temps/footer.php'); ?>

    </body>
</html>