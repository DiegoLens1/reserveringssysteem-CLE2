<?php

//placeholder so the there are no errors when u load the page for the first time
$name = '';
$email = '';

//checks if submit is present in the POST
if(isset($_POST['submit'])) {
    //includes the database credentials
    require_once "includes/database.php";

    /** @var mysqli $db */
    //stores name, email and password in variables
    $name = mysqli_escape_string($db, $_POST['naam']);
    $email = mysqli_escape_string($db, $_POST['email']);
    $password = mysqli_escape_string($db, $_POST['password']);

    $error = [];
    if($name == '') {
        //error name field empty
        $error['naam'] = 'Voer een naam in';
    }
    if($email == '') {
        //error email field empty
        $error['email'] = 'Voer een emailadress in';
    }
    if($password == '') {
        //error password field empty
        $error['password'] = 'Voer een wachtwoord in';
    }

    //checks if there are eny errors up until this point
    if(empty($error)) {
        //create and execute query to retrieve user data
        $query = "SELECT email FROM users WHERE email = '$email'";
        $result = mysqli_query($db, $query);
        //if amount of results does not equal 0 email adress is already in use
        if (mysqli_num_rows($result) == 0) {
            //hashes the password
            $password = password_hash($password, PASSWORD_DEFAULT);
            //creates and executes a query that inserts the user credentials in the database
            $query = "INSERT INTO users (naam, email, password) VALUES ('$name', '$email', '$password')";
            $result = mysqli_query($db, $query)
            or die('Db Error: '.mysqli_error($db).' with query: '.$query);

            //sends user to the login page after registering
            if ($result) {
                header('Location: login.php');
                exit;
            }
        } else {
            //error email is already in use
            $error['email'] = 'Dit email adress is al in gebruik';
        }
    }
}
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Registreren</title>
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
        </div>
        <main class="loginMain">
            <div class="formWrapper">
                <div class="title">Registreren</div>
                <form action="" method="post">
                    <div class="input-field">
                        <label for="naam">naam</label>
                        <input id="naam" type="text" name="naam" value="<?= htmlentities($name) ?? '' ?>">
                        <span class="error"><?= $error['naam'] ?? '' ?></span>
                    </div>
                    <div class="input-field">
                        <label for="email">Email</label>
                        <input id="email" type="email" name="email" value="<?= htmlentities($email) ?? '' ?>">
                        <span class="error"><?= $error['email'] ?? '' ?></span>
                    </div>
                    <div class="input-field">
                        <label for="password">Wachtwoord</label>
                        <input id="password" type="password" name="password" value="">
                        <span class="error"><?= $error['password'] ?? '' ?></span>
                    </div>
                    <div class="input-submit">
                        <input class="button" type="submit" name="submit" value="Registreren">
                    </div>
                </form>
            </div>
        </main>
    </div>
</body>
</html>
