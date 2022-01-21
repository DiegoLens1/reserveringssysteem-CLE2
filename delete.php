<?php
session_start();

//checks if the user is logged in and has admin rights
if (!isset($_SESSION['loggedInUser']) || $_SESSION['loggedInUser']['admin'] != 1) {
    header("Location: login.php");
    exit;
}

//if there is no id in the GET or id is empty send the user back to the appointment overview page
if(!isset($_GET['id']) || $_GET['id'] == '') {
    // redirect to afspraken.php
    header('Location: afspraken.php');
    exit;
}

//includes the database credentials
/** @var mysqli $db */
require_once"includes/database.php";

//stores the id from the GET in a variable
$appointmentId = mysqli_escape_string($db, $_GET['id']);


//creates and executes a query that retrieves appointment data drom the database
$query = "SELECT * FROM afspraken WHERE id = '$appointmentId'";
$result = mysqli_query($db, $query) or die('Error: '.mysqli_error($db). ' with query '. $query);

//if the amount of results does not equal 1 send the user back to the appointment overview page
if(mysqli_num_rows($result) != 1)
{
    header('Location: afspraken.php');
    exit;
}

//puts the results from the query in an asociative array and stores it in a variable
$appointment = mysqli_fetch_assoc($result);

//checks if submit is present in the POST
if(isset($_POST['submit'])){
    //creates and executes a query that deletes an appointment from that database
    $query = "DELETE FROM afspraken WHERE id = '$appointmentId'";
    mysqli_query($db, $query) or die ('Error: ' . mysqli_error($db));

    //close the connection to the database
    mysqli_close($db);

    //send the user back to the appointment overview page
    header("Location: afspraken.php");
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
            <div class="deleteWrapper">
                <div>Weet u zeker dat u deze afspraak wilt verwijderen?</div>
                <div>Naam:</div>
                <div><?= $appointment['naam'] ?></div>
                <div>Locatie:</div>
                <div><?= $appointment['locatie'] ?></div>
                <div>Datum:</div>
                <div><?= date('d-m-Y',strtotime($appointment['datumtijd'])) ?></div>
                <div>Tijd:</div>
                <div><?= date('H:i',strtotime($appointment['datumtijd'])) ?></div>
                <form action="" method="post">
                    <input type="hidden" name="id" value="<?= $appointment['id'] ?>">
                    <input class="delete" name="submit" type="submit" value="Verwijderen">
                </form>
            </div>
        </div>
    </main>
</div>
</body>
</html>