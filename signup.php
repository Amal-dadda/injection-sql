<?php
session_start();


if ($_SERVER['REQUEST_METHOD'] !== 'POST' && !isset($_SESSION['error_msg'])) {
    unset($_SESSION['firstName'], $_SESSION['lastName'], $_SESSION['email']);
}

$db_server = "localhost";
$db_user = "root";
$db_password = "admin";
$db_name = "singup";

try {
    $connection = new PDO("mysql:host=$db_server;dbname=$db_name", $db_user, $db_password);
} catch (PDOException $e) {
    die("Could not connect to database: " . $e->getMessage());
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["submit"])) {
    $nom = $_POST["firstName"];
    $prenom = $_POST["lastName"];
    $email = $_POST["email"];

    $newpassword = $_POST["password"];


    // le mot de passe doit avoir au moin 12 caractere avec des chiffres, des lettres majuscules et minusculeset des symbols comme (!@#%&^) ;
    //exemple d'un mdp valide: AmalDadda123! 
    if (!preg_match('/^(?=.*[A-Za-z])(?=.*\d)(?=.*[!@#$%^&*])[A-Za-z\d!@#$%^&*]{12,}$/', $newpassword)) {
        $_SESSION['error_msg'] = "Password must be at least 12 characters long and contain letters, numbers, and symbols (!@#$%^&*).";
        $_SESSION['firstName'] = $nom;
        $_SESSION['lastName'] = $prenom;
        $_SESSION['email'] = $email;
        header("Location: signup.php");
        exit();
    } else {

        $password = password_hash($newpassword, PASSWORD_DEFAULT); //hashage du mdp 

        //utilisation des requêtes préparées
        $req = $connection->prepare("INSERT INTO user VALUES (0, :nom, :prenom, :email, :pass ,'')");
        $req->execute([
            "nom" => $nom,
            "prenom" => $prenom,
            "email" => $email,
            "pass" => $password,
        ]);

        unset($_SESSION['firstName'], $_SESSION['lastName'], $_SESSION['email'], $_SESSION['error_msg']);

        header("Location: login.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Registration Form</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-color: #f1f5f9;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .form-container {
            background-color: white;
            padding: 2rem;
            border-radius: 16px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            width: 350px;
        }

        .form-container h2 {
            margin-bottom: 1.5rem;
            font-size: 24px;
            color: #0f172a;
            text-align: center;
        }

        .form-group {
            margin-bottom: 1rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: #334155;
        }

        .form-group input {
            width: 100%;
            padding: 0.75rem;
            border-radius: 12px;
            border: 1px solid #cbd5e1;
            background-color: #f8fafc;
            outline: none;
            transition: border 0.3s;
        }

        .form-group input:focus {
            border-color: #0f172a;
        }

        .submit-btn {
            background-color: #0f172a;
            color: white;
            padding: 0.75rem;
            width: 100%;
            border: none;
            border-radius: 12px;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .submit-btn:hover {
            background-color: #1e293b;
        }

        .error-message {
            color: red;
            font-size: 14px;
            margin-bottom: 1rem;
            text-align: center;
        }

        .login-link {
            display: block;
            text-align: center;
            margin-bottom: 1rem;
            font-size: 0.9rem;
            color: #64748b;
            text-decoration: none;
        }

        .login-link:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <div class="form-container">
        <h2>Register</h2>
        <form method="POST" action="">
            <div class="form-group">
                <label for="firstName">First Name</label>
                <input type="text" id="firstName" name="firstName" required placeholder="Enter your first name"
                    value="<?= isset($_SESSION['firstName']) ? htmlspecialchars($_SESSION['firstName']) : '' ?>">
            </div>
            <div class="form-group">
                <label for="lastName">Last Name</label>
                <input type="text" id="lastName" name="lastName" required placeholder="Enter your last name"
                    value="<?= isset($_SESSION['lastName']) ? htmlspecialchars($_SESSION['lastName']) : '' ?>">
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required placeholder="Enter your email"
                    value="<?= isset($_SESSION['email']) ? htmlspecialchars($_SESSION['email']) : '' ?>">
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required placeholder="Enter your password">
            </div>

            <a class="login-link" href="login.php">I already have an account</a>

            <?php if (isset($_SESSION['error_msg'])): ?>
                <div class="error-message"><?= htmlspecialchars($_SESSION['error_msg']) ?></div>
                <?php unset($_SESSION['error_msg']); ?>
            <?php endif; ?>

            <input type="submit" value="Submit" name="submit" class="submit-btn">
        </form>
    </div>
</body>

</html>
