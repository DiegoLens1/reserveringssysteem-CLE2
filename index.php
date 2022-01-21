<?php
session_start();

//check if user is logged in
if (!isset($_SESSION['loggedInUser'])) {
    header("Location: login.php");
    exit;
}
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Home</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="wrapper">
        <div class="header">
            <a href="index.php"><img src="img/aanzet_logo_vierkant_transparant.png" alt="Aanzet logo"></a>
            <nav>
                <a href="afspraken.php">Afspraken</a>
                <a href="">Handleiding</a>
                <a href="">Veelgestelde vragen</a>
            </nav>
            <?php if (isset($_SESSION['loggedInUser'])){ ?>
                <div class="profile">
                    <a href=""><img src="img/default-avatar.png" alt="profiel foto"><?= $_SESSION['loggedInUser']['naam'] ?></a>
                    <a href="logout.php">Uitloggen</a>
                </div>
            <?php } ?>
        </div>
        <main class="homeMain">
            <div class="homeWrapper">
                <div class="title">Home</div>
                <a class="button" href="afspraak_maken.php">Afspraak maken</a>
                <a class="button" href="afspraken.php"> Afspraken overzicht</a>
            </div>
        </main>
    </div>
</body>
</html>