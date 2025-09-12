<?php

session_start();
include 'components/connect.php';
// include 'components/connection.php';
// require __DIR__ . "/components/connect.php";

//new
if(isset($_SESSION['user_id'])){
   $user_id = $_SESSION['user_id'];
}else{
   $user_id = '';
};
//
$errors = [];
$username = "";
$password = "";
$email ="";
$password = $Cpassword = "";
// $success = false;
// $errors = $_SESSION['errors'] ?? [];
// $username = $_SESSION['old_username'] ?? "";
// $email = $_SESSION['old_email'] ?? "";
// unset($_SESSION['errors'], $_SESSION['old_username'], $_SESSION['old_email']);
// $password = $Cpassword = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // ----------- VALIDATIONS ------------

    // username
    if (empty($_POST["username"])) {
        $errors["username"] = "Username is required";
    } else {
        $username = sanitizeInput($_POST["username"]);
        if (strlen($username) < 5) {
            $errors["username"] = "Username must be at least 5 characters long";
        } elseif (!preg_match("/^[a-zA-Z0-9._-]+$/", $username)) {
            $errors["username"] = "Username can only contain letters, numbers, periods, dashes, or underscores";
        }
    }

    // password
    if (empty($_POST["password"])) {
        $errors["password"] = "Password is required";
    } else {
        $password = sanitizeInput($_POST["password"]);
        if (strlen($password) < 8) {
            $errors["password"] = "Password must be at least 8 characters long";
        } elseif (!preg_match("/[@#$%]/", $password)) {
            $errors["password"] = "Password must contain at least one special character";
        }
    }

    // confirm password
    if (empty($_POST["Cpassword"])) {
        $errors["Cpassword"] = "Confirm your password";
    } else {
        $Cpassword = $_POST["Cpassword"];
        if ($password !== $Cpassword) {
            $errors["Cpassword"] = "Passwords do not match";
        }
    }

    // email
    if (empty($_POST["email"])) {
        $errors["email"] = "Email is required";
    } else {
        $email = sanitizeInput($_POST["email"]);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors["email"] = "Invalid email format";
        }
    }

    // ----------   IF NO ERRORS, INSERT INTO DB ------------
    if (empty($errors)) {
        $success = true;

        // hash password before storing
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

$stmt = $conn->prepare("INSERT INTO all_users(username, password, email, type) VALUES (?, ?, ?, 'user')");
$inserted = $stmt->execute([$username, $hashedPassword, $email]);

if ($inserted) {
    $last_id = $conn->lastInsertId();
    $select_user = $conn->prepare("SELECT * FROM all_users WHERE id = ?");
    $select_user->execute([$last_id]);
    $row = $select_user->fetch(PDO::FETCH_ASSOC);

    $_SESSION["username"] = $row["username"];
    $_SESSION["user_id"] = $row["id"];
    $_SESSION["type"] = $row["type"];

    header("Location: home.php");
    exit();
} else {
    echo "Database Error: Could not insert user.";
}

    } else {
        // store errors & old input
        // $_SESSION['errors'] = $errors;
        // $_SESSION['old_username'] = $username;
        // $_SESSION['old_email'] = $email;
        header("Location: signup.php");
        exit();
    }
}

function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}
?>




<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="style2.css">
</head>

<body>

    <div id="main">

        <!-- <legend>Log In</legend> -->
        <div id="left_div">

            <img src="./images/signUp.jpg
        " alt="">
        </div>
        <div id="right_div">

            <form action="signup.php" method="POST">
                <div id="logo"> <img id="cafeicon" src="./icon/cafe.png" alt="">
                    <p id="yourcafe"><i>Cozy Cafe</i></p>
                </div>
                <h1>Register Here!</h1>

                <div id="username">
                    <label for="username" name="username" id="username">Username</label>
                    <input type="text" name="username" value="<?php echo $username;?>">
                    <?php if (isset($errors["username"])): ?>
                    <span class="error"><?php echo $errors["username"]; ?></span>
                    <?php endif; ?>

                </div>
                <div id="password">
                    <label for="password" name="password" id="password">Password</label>
                    <input type="password" name="password">
                    <?php if (isset($errors["password"])): ?>
                    <span class="error"><?php echo $errors["password"]; ?></span>
                    <?php endif; ?>

                </div>
                <div id="Cpassword">
                    <label for="Cpassword" name="Cpassword" id="Cpassword">Confirm Password</label>
                    <input type="password" name="Cpassword">
                    <?php if (isset($errors["Cpassword"])): ?>
                    <span class="error"><?php echo $errors["Cpassword"]; ?></span>
                    <?php endif; ?>
                </div>
                <div id="email">
                    <label for="email" name="email" id="email">Email</label>
                    <input type="email" name="email" value="<?php echo $email;?>">
                    <?php if (isset($errors["email"])): ?>
                    <span class=" error"><?php echo $errors["email"]; ?></span>
                    <?php endif; ?>
                </div>
                <input type="submit" id="submit" name="submit" value="Register">

            </form>
        </div>

    </div>


</body>

</html>