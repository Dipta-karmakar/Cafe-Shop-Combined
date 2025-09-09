<?php
session_start();
require __DIR__ . "/components/connection.php";

$errors = [];
$username = "";
$password = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (empty($_POST["username"])) {
        $errors["username"] = "Username is required";
    } else {
        $username = trim($_POST["username"]);
    }

    if (empty($_POST["password"])) {
        $errors["password"] = "Password is required";
    } else {
        $password = $_POST["password"];
    }

    if (empty($errors)) {
        $stmt = $conn->prepare("SELECT id, username, password, type FROM all_users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $row = $result->fetch_assoc();

            // ✅ verify password (your sample passwords are not hashed yet)
            if (password_verify($password, $row["password"]) || $password === $row["password"]) {
                $_SESSION["username"] = $row["username"];
                $_SESSION["user_id"] = $row["id"];
                $_SESSION["type"] = $row["type"];

                // ✅ redirect by type
                if ($row["type"] === "admin") {
                    header("Location: admin/dashboard.php");



                } elseif ($row["type"] === "employee") {
                    header("Location: employee_dashboard.php");
                } else {
                    header("Location: home.php");
                }
                exit();
            } else {
                $errors["login"] = "Invalid username or password";
            }
        } else {
            $errors["login"] = "Invalid username or password";
        }

        $stmt->close();
    }
}

$conn->close();
?>


<!DOCTYPE html>

<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div id="main">
        <!-- <legend>Log In</legend> -->

        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
            <div id="logo"> <img id="cafeicon" src="./icon/cafe.png" alt="">
                <p id="yourcafe"><i>Cozy Cafe</i></p>
            </div>
            <h1>Log In</h1>

            <div id="username">
                <label for="username" name="username">Username</label>
                <input type="text" name="username" value="<?php echo $username;?>">
                <?php if(isset($errors["username"])): ?> <span class="error"><?php echo $errors["username"]; ?></span>
                <?php endif; ?>

            </div>
            <div id="password">
                <label for="password" name="password">Password</label>
                <input type="password" name="password">
                <?php if (isset($errors["password"])): ?>
                <span class="error"><?php echo $errors["password"]; ?></span>
                <?php endif; ?>

            </div>
            <input type="submit" id="submit" name="submit" value="Log In">
            <p id="registerLink">Dont haves an account? <a href="signup.php ">Register</a> </p>
        </form>
    </div>
</body>

</html>