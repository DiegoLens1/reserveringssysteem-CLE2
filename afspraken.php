<?php
session_start();

//check if user is logged in and has admin rights
if (!isset($_SESSION['loggedInUser']) || $_SESSION['loggedInUser']['admin'] != 1) {
    header("Location: login.php");
    exit;
}

//includes the database credentials
/** @var mysqli $db */
require_once('includes/database.php');

//create and execute query retrieve appointments from database
$query ="SELECT *, afspraken.id AS afspraken_id FROM afspraken INNER JOIN users ON afspraken.user_id = users.id";
$result = mysqli_query($db, $query) or die ('Error' . $query);

//put results from query in array
$appointments =[];
while ($row = mysqli_fetch_assoc($result)) {
    $appointments[] = $row;
}

//sort the appoitnments by date and time
usort($appointments, function($a, $b){
    return new Datetime($a['datumtijd']) <=> new DateTime($b['datumtijd']);
});

mysqli_close($db);
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Afspraken</title>
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
        <main>
            <div class="appointments">
                <div>Eerstvolgenden afspraken</div>
                <div class="appointmentList">
                    <?php foreach ($appointments as $appointment){ ?>
                    <a href="afspraak_bewerken.php?id=<?= $appointment['afspraken_id'] ?>" class="appointmentItem">
                        <div class="appointmentId"><?= $appointment['afspraken_id'] ?></div>
                        <div class="appointmentName"><?= $appointment['naam'] ?></div>
                        <div class="appointmentLocation"><?= $appointment['locatie'] ?></div>
                        <div class="appointmentDate"><?= date('d-m-Y', strtotime($appointment['datumtijd'])) ?></div>
                        <div class="appointmentTime"><?= date('H:i', strtotime($appointment['datumtijd'])) ?></div>
                    </a>
                    <?php } ?>
                </div>
            </div>
            <div class="calendarWrapper">
                Calendar
                <div class="calendarFrame">
                    <?= print_r($_SESSION) ?>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
