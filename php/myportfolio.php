<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome</title>
    <link rel="stylesheet" href="../css/myportfolio.css">
</head>

<body>
    <header class="header">
        <img src="../images/kzlogo.png" alt="kzLogo" class="kzLogo">
        <ul class="links">

            <?php if (isset($_SESSION["user_id"])): ?>
                <?php if ($_SESSION["role"] === "admin"): ?>
                    <li><a href="../php/myfiles.php">My files</a></li>
                <?php else: ?>
                    <li><a href="../php/files.php">My files</a></li>
                <?php endif; ?>
            <?php endif; ?>

            <li>About me</li>
            <?php if (isset($_SESSION["user_id"])): ?>
                <li class="loginButton">
                    <a href="../php/logout.php">Log out</a>
                </li>
            <?php else: ?>
                <li class="loginButton">
                    <a href="../html/login.html">Log in</a>
                </li>
            <?php endif; ?>
        </ul>
    </header>
    <div class="introductionContainer">
        <div class="welcomeBlock">
            <img src="../images/myphoto.jpg" alt="myPhoto" class="myPhoto">

            <div class="welcomeText">
                <h1>Hi, I'm Klaudia!</h1>
                <p class="shortIntro">I am a first year IT student, and this is my small portfolio.</p>
                <p class="shortIntro">If you want to get to know me better, feel free to visit an <a
                        href="aboutme.html">About me</a> page.
                </p>
            </div>
        </div>
    </div>
    <div class="educationContainer">
        <h2>Education</h2>
        <div class="timeline">
            <div class="timelineItem">
                <h3>September 2025</h3>
                <p>Started studying IT at NHL Stenden</p>
            </div>
            <div class="timelineItem">
                <h3>November 2025</h3>
                <p>Started creating my personal portfolio website</p>
            </div>
        </div>
    </div>

</body>

</html>