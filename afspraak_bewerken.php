<?php
session_start();

//checks if the user is logged in and has admin rights
if (!isset($_SESSION['loggedInUser']) || $_SESSION['loggedInUser']['admin'] != 1) {
    header("Location: login.php");
    exit;
}

//if there is no id on the GET or id is empty send the user back to the appointment overview page
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

//creates and executes a query to retrieve appointment data
$query = "SELECT *, afspraken.id AS afspraken_id FROM afspraken INNER JOIN users ON afspraken.user_id = users.id WHERE afspraken.id = $appointmentId";
$result = mysqli_query($db, $query) or die('Error: '.mysqli_error($db). ' with query '. $query);

//if amount of results is not equal to 1 send user back to appointment overview page
if(mysqli_num_rows($result) != 1)
{
    header('Location: afspraken.php');
    exit;
}

//puts the results from the query in an asociative array and stores it in a variable
$afspraak = mysqli_fetch_assoc($result);

//checks if submit is present in the POST
if(isset($_POST['submit'])) {
    //stores the data from the input fields in variables
    $name       = mysqli_escape_string($db, $_POST['naam']);
    $location    = mysqli_escape_string($db, $_POST['locatie']);
    $date      = mysqli_escape_string($db, $_POST['datum']);
    $time       = mysqli_escape_string($db, $_POST['tijd']);

    //converts the date and time into a single datetime
    $datetime = date("Y-m-d H:i:s", strtotime($date . $time));

    //includes the page that contains errors
    require_once "includes/form-validations.php";

    //checks if there are any errors up to this point
    if(empty($error)) {
        //creates and executes a query that updates and appointment in the database
        $query = "UPDATE afspraken INNER JOIN users ON afspraken.user_id = users.id 
                SET naam = '$name', locatie = '$location', datumtijd = '$datetime' 
                WHERE afspraken.id = '$appointmentId'";
        $result = mysqli_query($db, $query) or die('Error: '.mysqli_error($db). ' with query '. $query);

        //if the query executed successfully send the user back to the appointment overview page
        if($result) {
            header('location: afspraken.php');
            exit;
        } else {
            //error something went wrong in the query
            $error['$db'] = 'Er is iets mis gegaan in de query: '. mysqli_error($db);
        }
    }
    //close connection to the database
    mysqli_close($db);
}
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Afspraak bewerken</title>
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
    <main class="editAppointmentMain">
        <div class="formWrapper">
            <div class="title">Afspraak bewerken</div>
            <form action="" method="post" enctype="multipart/form-data">
                <div class="input-field">
                    <label for="naam">Naam kind</label>
                    <input id="naam" type="text" name="naam" value="<?= isset($name) ? htmlentities($name) : htmlentities($afspraak['naam']) ?>">
                    <span class="error"><?= $error['naam'] ?? '' ?></span>
                </div>
                <div class="input-field">
                    <label for="locatie">locatie</label>
                    <input id="locatie" type="text" name="locatie" value="<?= isset($location) ? htmlentities($location) : htmlentities($afspraak['locatie']) ?>">
                    <span class="error"><?= $error['locatie'] ?? '' ?></span>
                </div>
                <div class="input-field">
                    <label for="datum">datum (maandag t/m vrijdag)</label>
                    <input id="datum" type="date" name="datum" value="<?= isset($date) ? htmlentities($date) : htmlentities(substr($afspraak['datumtijd'], 0, 10)) ?>">
                    <span class="error"><?= $error['datum'] ?? '' ?></span>
                </div>
                <div class="input-field">
                    <label for="tijd">tijd (09:00 t/m 17:00)</label>
                    <input id="tijd" type="time" name="tijd" value="<?= isset($time) ? htmlentities($time) : htmlentities(substr($afspraak['datumtijd'], 11, 5)) ?>">
                    <span class="error"><?= $error['tijd'] ?? '' ?></span>
                </div>
                <div class="input-submit">
                    <input class="button" type="submit" name="submit" value="Afspraak bewerken">
                </div>
                <a class="delete" href="delete.php?id=<?= $appointmentId ?>">Afspraak verwijderen</a>
            </form>
        </div>
    </main>
</div>
</body>
</html>