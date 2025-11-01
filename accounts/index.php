<?php

    session_start();

    include('/home/users/web/b2283/ipg.stinttrackercom/home/db_files/connection.php');
    include('/home/users/web/b2283/ipg.stinttrackercom/home/functions.php');

    //if the user isn't logged in, redirect to the home page
    if(!isset($_SESSION['username']))
    {
        header('Location: /home/');
    }

    $sql = "SELECT `id` FROM `users` WHERE `users`.`username` = '" . $_SESSION['username'] . "';";
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($result);
    $user_id = $row['id'];
    $sql = "SELECT `drill_id`, `timestamp` FROM `drill_results` WHERE `drill_results`.`user_id` = '" . $user_id . "' ORDER BY `timestamp` DESC;";
    $drill_results = mysqli_query($conn, $sql);
    if (mysqli_num_rows($drill_results) > 0)
    {
        $user_has_results = true;
        $first_row = true;
        $completed_drills = array();
        while ($row = mysqli_fetch_assoc($drill_results))
        {
            if ($first_row)
            {
                $first_row = false;
                $latest_drill = $row;
                $sql = "SELECT `name` FROM `drills` WHERE `drills`.`drill_id` = '" . $latest_drill['drill_id'] . "';";
                $latest_drill['name'] = mysqli_fetch_assoc(mysqli_query($conn, $sql))['name'];
                $latest_drill['date_string'] = get_timestamp_in_english($latest_drill['timestamp']);        
            }
            if (!in_array(intval($row['drill_id']), $completed_drills))
            {
                array_push($completed_drills, intval($row['drill_id']));
            }
        }
    }
    else
    {
        $user_has_results = false;
    }
?>

<!DOCTYPE html>

<html>
    <head>
<script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-0531879237494766" crossorigin="anonymous"></script>
        <title>
            My Account - Pool Practice Tracker
        </title>
        <link rel="stylesheet" type="text/css" href="/home/styles/general.css">
        <link rel="stylesheet" type="text/css" href="/home/styles/header.css">
        <link rel="stylesheet" type="text/css" href="/home/styles/user-handling.css">
        <link rel="stylesheet" type="text/css" href="/home/styles/accounts_page.css">
    </head>
    <body>
        
        <?php include('/home/users/web/b2283/ipg.stinttrackercom/home/temps/header.php'); ?>

        <main class="main-section">
            <div class="accounts-form-header">
                <h2>My Account</h2>
            </div>
            <form class="accounts-form" action="/home/index.php " method="post">
                <div class="input-group">
                    <h3>Hello, <?php echo $_SESSION['username']; ?>!</h3>
                    <?php if($user_has_results): ?>
                        <p>
                            The last drill you completed was <b>Drill #<?php echo $latest_drill['drill_id'] . " - " . $latest_drill['name']; ?></b> on <?php echo $latest_drill['date_string']; ?>.
                        </p>
                        <p>
                            <b>Your completed drills:</b>
                            <?php foreach ($completed_drills as $drill_id): ?>
                                
                                <?php $sql = "SELECT `name` FROM `drills` WHERE `drill_id` = " . $drill_id . ";"; ?>
                                <?php $drill_name = mysqli_fetch_assoc(mysqli_query($conn, $sql))['name']; ?>
                                <p><a href="/home/drills/drill_results.php?drill_num=<?php echo $drill_id; ?>" class="drill_results_link">
                                    <?php echo "Drill #" . $drill_id . " - " . $drill_name; ?>
                                </a></p>
                            <?php endforeach; ?>
                        </p>
                    <?php else: ?>
                        You have not completed any drills yet.
                    <?php endif; ?>
                </div>
            </form>
        </main>

        <?php include('/home/users/web/b2283/ipg.stinttrackercom/home/temps/footer.php'); ?>

    </body>
</html>