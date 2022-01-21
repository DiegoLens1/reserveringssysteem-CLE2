<?php
session_start();

//checks if the user is logged in
if (!isset($_SESSION['loggedInUser'])) {
    header("Location: login.php");
    exit;
}

//checks if submit is present in the POST
if(isset($_POST['submit'])) {
    //includes the database credentials
    /** @var mysqli $db */
    require_once"includes/database.php";

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
        //creates and executes a query that inserts the appointment data in the database
        $query ="INSERT INTO afspraken(naam, locatie, datumtijd) VALUES('$name', '$location', '$datetime')";
        $result = mysqli_query($db, $query) or die('Error: '.mysqli_error($db). ' with query '. $query);

        //if the query executed successfully send the user to the appointment overview page
        if($result) {
            header('location: afspraken.php');
            exit;
        } else {
            //error something went wrong with the query
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
    <title>Afspraak maken</title>
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
        <main class="makeAppointmentMain">
            <div class="formWrapper">
                <div class="title">Afspraak maken</div>
                <form action="" method="post" enctype="multipart/form-data">
                    <div class="input-field">
                        <label for="naam">Naam kind</label>
                        <input id="naam" type="text" name="naam" value="<?= isset($name) ? htmlentities($name) : '' ?>">
                        <span class="error"><?= $error['naam'] ?? '' ?></span>
                    </div>
                    <div class="input-field">
                        <label for="locatie">locatie</label>
                        <input id="locatie" type="text" name="locatie" value="<?= isset($location) ? htmlentities($location) : '' ?>">
                        <span class="error"><?= $error['locatie'] ?? '' ?></span>
                    </div>
                    <div class="input-field">
                        <label for="datum">datum (maandag t/m vrijdag)</label>
                        <input id="datum" type="date" name="datum" value="<?= isset($date) ? htmlentities($date) : '' ?>">
                        <span class="error"><?= $error['datum'] ?? '' ?></span>
                    </div>
                    <div class="input-field">
                        <label for="tijd">tijd (09:00 t/m 17:00)</label>
                        <input id="tijd" type="time" name="tijd" value="<?= isset($time) ? htmlentities($time) : '' ?>">
                        <span class="error"><?= $error['tijd'] ?? '' ?></span>
                    </div>
                    <div class="input-submit">
                        <input class="button" type="submit" name="submit" value="Afspraak maken">
                    </div>
                </form>
            </div>
        </main>
    </div>
</body>
</html>