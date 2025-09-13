<?php
session_start();
require_once("dbcontroller.php");
$db_handle = new DBController();

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}
$user_id = intval($_SESSION['user_id']);

// Fetch current user info
$user = $db_handle->runQuery("SELECT * FROM all_users WHERE id = $user_id");
if ($user && count($user) > 0) {
    $user = $user[0];
} else {
    die("User not found.");
}

$success = $error = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_address'])) {
        $address = mysqli_real_escape_string($db_handle->getConn(), $_POST['address']);
        $update_address = "UPDATE all_users SET address = '$address' WHERE id = $user_id";
        mysqli_query($db_handle->getConn(), $update_address);
        $success = "Address updated successfully.";
        $user = $db_handle->runQuery("SELECT * FROM all_users WHERE id = $user_id")[0];
    }
    if (isset($_POST['update_password'])) {
        $password = $_POST['password'];
        $confirm = $_POST['confirm_password'];
        if ($password === $confirm && !empty($password)) {
            $password = mysqli_real_escape_string($db_handle->getConn(), $password);
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $update_pass = "UPDATE all_users SET password = '$hashed' WHERE id = $user_id";
            mysqli_query($db_handle->getConn(), $update_pass);
            $success = "Password updated.";
        } else {
            $error = "Passwords do not match or are empty.";
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Settings</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="style.css" type="text/css" rel="stylesheet" />
</head>
<body>
<div class="settings-container">
    <h2>Account Settings</h2>
    <?php if ($success) { echo '<div class="success">'.$success.'</div>'; } ?>
    <?php if ($error) { echo '<div class="error">'.$error.'</div>'; } ?>
    <form method="post" action="" style="margin-bottom:2em;">
        <label for="address">Address:</label><br>
        <textarea id="address" name="address" rows="3" cols="40" required><?php echo htmlspecialchars($user['address']); ?></textarea><br><br>
        <input type="submit" name="update_address" value="Update Address">
    </form>
    <form method="post" action="">
        <label for="password">New Password:</label><br>
        <input type="password" id="password" name="password" required><br><br>
        <label for="confirm_password">Rewrite Password:</label><br>
        <input type="password" id="confirm_password" name="confirm_password" required><br><br>
        <input type="submit" name="update_password" value="Update Password">
    </form>
</div>
</body>
</html>
