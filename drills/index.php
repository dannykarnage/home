<?php

    session_start();
    //include('/home/users/web/b2283/ipg.stinttrackercom/home/functions.php');
    include('/home/users/web/b2283/ipg.stinttrackercom/home/db_files/connection.php');
    $error_message = "";


    // Fetch published drills (Secure)
    $stmt = $conn->prepare("SELECT `drill_id`, `name`, `has_table_diagram` FROM `drills` WHERE `published` = 1 ORDER BY `drill_id` DESC");
    
    if(!$stmt)
    {
        $error_message = "Could not prepare database query. Please try again later.";
    }
    else
    {
        $stmt->execute();
        $result = $stmt->get_result();
        
        if(!$result)
        {
            $error_message = "Could not connect to database. Please try again later.";
        }
    }
?>

<!DOCTYPE html>
<html>
    <head>
<script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-0531879237494766" crossorigin="anonymous"></script>
        <title>Drills - Pool Practice Tracker</title>
        <link rel="stylesheet" href="/home/styles/general.css">
        <link rel="stylesheet" href="/home/styles/header.css">
        <link rel="stylesheet" type="text/css" href="/home/styles/user-handling.css">
        <link rel="stylesheet" type="text/css" href="/home/styles/drills.css">
    </head>
    <body style="padding-bottom: 40px;">

        <?php include('/home/users/web/b2283/ipg.stinttrackercom/home/temps/header.php'); ?>

        <main class="main-section">
            <div class="page-subtitle">
                Drills
            </div>
            <?php if(empty($error_message)): ?>
                <div class="three-image-grid" style="margin-top: 0px;"> 
                    <div class="right-section">
                    </div>
                    <div class="middle-section">
                        <!--<form action="/home/drills/drills_by_category.php" method="post">-->
                        <form action="/home/this-link-doesnt-work.html" method="post">
                            <div class="input-group">
                                <button id="button" type="submit" value="drills_by_category" name="drills_by_category" class="user-form-btn" style="cursor: pointer;">Display drills by Category</button>
                            </div>
                        </form>
                    </div>
                    <div class="left-section">
                    </div>
                </div>
                <div class="drills-table">
                    <div class="table_header">
                        <div class="table_header_item">
                            Drill #
                        </div>
                        <div class="table_header_item">
                            Table Diagram
                        </div>
                        <div class="table_header_item">
                            Drill Name
                        </div>
                    </div>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <?php echo '<a href="/home/drills/drill_details.php?drill_id=' . htmlspecialchars($row['drill_id']) . '">'; ?>
                        <div class="drill_row">
                            <div class="drill_num">
                                <?php echo htmlspecialchars($row['drill_id']); ?>
                            </div>
                            <div class="table_diagram">
                                <?php if($row['has_table_diagram']): ?>
                                    <?php echo '<img src="/home/drills/diagrams/' . htmlspecialchars($row['drill_id']) . '.png">'; ?>
                                <?php endif; ?>
                            </div>
                            <div class="drill_name">
                                <?php echo htmlspecialchars($row['name']); ?>
                            </div>
                        </div>
                        <?php echo '</a>'; ?>
                    <?php endwhile; ?>
                </div>
                <?php $stmt->close(); ?>
            <?php else: ?>
                <div class="ueser-form-error" style="width: 600px;">
                    <?php echo htmlspecialchars($error_message); ?>
                </div>
            <?php endif; ?>
        </main>

        <?php include('/home/users/web/b2283/ipg.stinttrackercom/home/temps/footer.php'); ?>

    </body>
</html>
