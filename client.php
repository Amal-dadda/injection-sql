<?php
session_start();
$db_server = "localhost";
$db_user = "root";
$db_password = "admin";
$db_name = "singup";
$error = "";

try {
    $connection = new PDO("mysql:host=$db_server;dbname=$db_name", $db_user, $db_password);
} catch (PDOException $e) {
    die("Could not connect to database: " . $e->getMessage());
}

$email = $_COOKIE['email'];
$token = $_COOKIE['token'];

if ($token) {
    $req = $connection->prepare("SELECT * FROM user WHERE email = :email AND token = :token ");

    $req->execute(array(
        'email' => $email,
        'token' => $token,
    ));
    $rep = $req->fetch(PDO::FETCH_ASSOC);

?>


    <!DOCTYPE html>
    <html lang="fr">

    <head>
        <meta charset="UTF-8">
        <title>Dashboard</title>
        <style>
            body {
                background-color: #f1f5f9;
                display: flex;
                justify-content: center;
                align-items: center;
                height: 100vh;
                font-family: Arial, sans-serif;
            }

            .message-box {
                text-align: center;
                background-color: #ecfdf5;
                border: 2px solid #0f172a;
                color: #059669;
                padding: 2rem;
                border-radius: 12px;
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            }

            .message-box h1 {
                margin-bottom: 1rem;
            }

            .logout-btn {
                display: inline-block;
                margin-top: 1rem;
                padding: 0.5rem 1.2rem;
                background-color: #0f172a;
                color: white;
                text-decoration: none;
                border-radius: 8px;
                font-weight: bold;
                transition: background-color 0.3s;
            }

            .logout-btn:hover {
                background-color: #059669;
            }
        </style>
    </head>

    <body>
        <div class="message-box">
            <?php
            if ($rep['nom'] && $rep['prenom']) {
            ?>


                <h1> <?php echo "Bienvune ! " . $rep['nom'] . " " . $rep['prenom'] . ". vous etes bien connecté !!"; ?> </h1>
        <?php
            }
        } else {
            header("location: login.php");
        }
        ?>

        <p>Vous êtes bien connecté </p>
        <a href="login.php" class="logout-btn">Se déconnecter</a>
        </div>
    </body>

    </html>