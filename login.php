<?php
session_start();

$db_server = "localhost";
$db_user = "root";
$db_password = "admin";
$db_name = "singup";
$connection = "";

try {
    $connection = new PDO("mysql:host=$db_server;dbname=$db_name", $db_user, $db_password);
} catch (PDOException $e) {
    $_SESSION['error_msg'] = "Could not connect to database.";
    header("Location: login.php");
    exit();
}

if (isset($_POST["submit"])) {
    if (!empty($_POST["email"]) && !empty($_POST["password"])) {

        $token = bin2hex(random_bytes(32));
        $email = $_POST["email"];
        $password = $_POST["password"];

        $req = $connection->prepare("SELECT * FROM user WHERE email = :email");
        $req->bindValue('email', $email);
        $req->execute();
        $rep = $req->fetch(PDO::FETCH_ASSOC);

        if ($rep) {
            $passwordhash = $rep['pass'];

            if (password_verify($password, $passwordhash)) {
                $token_req = $connection->prepare("UPDATE user SET token = :token WHERE email = :email AND pass = :pass");
                $token_req->execute([
                    'token' => $token,
                    'email' => $email,
                    'pass' => $passwordhash
                ]);

                setcookie('token', $token, time() + 120);
                setcookie('email', $email, time() + 120);
                header("location:client.php");
                exit();
            } else {
                $_SESSION['error_msg'] = "Mot de pass incorrect !";
                header("Location: login.php");
                exit();
            }
        } else {
            $_SESSION['error_msg'] = "Email ou mot de passe incorrect !";
            header("Location: login.php");
            exit();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Login</title>
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
            margin: 10px 0;
            text-align: center;
            font-weight: bold;
        }

        .signup-link {
            display: block;
            text-align: center;
            margin-bottom: 1rem;
            font-size: 0.9rem;
            color: #64748b;
            text-decoration: none;
        }

        .signup-link:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <div class="form-container">
        <h2>Login</h2>
        <form method="POST" action="">
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" placeholder="Enter your email" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Enter your password" required>
            </div>

            <a class="signup-link" href="signup.php">I don't have an account</a>


            <?php if (isset($_SESSION['error_msg'])): ?>
                <div class="error-message"><?= htmlspecialchars($_SESSION['error_msg']) ?></div>
                <?php unset($_SESSION['error_msg']); ?>
            <?php endif; ?>

            <input type="submit" value="Submit" name="submit" class="submit-btn">
        </form>
    </div>
</body>

</html>