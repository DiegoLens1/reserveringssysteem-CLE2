<?php
session_start();

//sets logged in status when u log in
if(isset($_SESSION['loggedInUser'])){
    $login = true;
} else {
    $login = false;
}

//includes the database credentials
/** @var mysqli $db */
require_once "includes/database.php";

//checks if submit is present in the POST
if (isset($_POST['submit'])) {
    //store email and password in variables
    $email = mysqli_escape_string($db, $_POST['email']);
    $password = $_POST['password'];

    $error = [];
    if($email == '') {
        //error empty email field
        $error['email'] = 'Voer een gebruikersnaam in';
    }
    if($password == '') {
        //error empty password field
        $error['password'] = 'Voer een wachtwoord in';
    }

    //checks if there are any errors up until this point
    if(empty($error)) {
        //creates and executes query to retrieve user data
        $query = "SELECT * FROM users WHERE email='$email'";
        $result = mysqli_query($db, $query);
        //if amount of results does not equal 1 send error
        if (mysqli_num_rows($result) == 1) {
            $user = mysqli_fetch_assoc($result);
            //if email and password are correct. set login status to true and save credentials in session
            if (password_verify($password, $user['password'])) {
                $login = true;

                $_SESSION['loggedInUser'] = [
                    'naam' => $user['naam'],
                    'email' => $user['email'],
                    'id' => $user['id'],
                    'admin' => $user['admin']
                ];
                //if logged in user is an admin send them to the appointment overview page. else send them to home
                if($_SESSION['loggedInUser']['admin'] == 1){
                    header('location: afspraken.php');
                    exit;
                } else {
                    header('locationL index.php');
                    exit;
                }
            } else {
                //error wrong credentials
                $error['loginFailed'] = 'Onbekend email en wachtwoord';
            }
        } else {
            //error wrong credentials
            $error['loginFailed'] = 'Onbekend email en wachtwoord';
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
    <title>Login</title>
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
            <?php if ($login) { ?>
                <div>
                    Je bent ingelogd!
                </div>
                <div><a href="logout.php">Uitloggen</a></div>
            <?php } else { ?>
                <div class="formWrapper">
                    <div class="title">Login</div>
                    <form action="" method="post">
                        <div class="input-field">
                            <label for="email">Email</label>
                            <input id="email" type="email" name="email" value="<?= isset($email) ? htmlentities($email) : '' ?>">
                            <span class="error"><?= $error['email'] ?? '' ?></span>
                        </div>
                        <div class="input-field">
                            <label for="password">Wachtwoord</label>
                            <input id="password" type="password" name="password" value="">
                            <span class="error"><?= $error['password'] ?? '' ?></span>
                        </div>
                        <div class="input-submit">
                            <p><span class="error"><?= $error['loginFailed'] ?? '' ?></span></p>
                            <input class="button" type="submit" name="submit" value="Login">
                        </div>
                    </form>
                    <div>
                        <a href="register.php">Nog geen acount? registreer je hier.</a>
                    </div>
                </div>
            <?php } ?>
        </main>
    </div>
</body>
</html>
