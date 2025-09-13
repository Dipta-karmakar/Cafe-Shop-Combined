<?php
session_start();
require_once("dbcontroller.php");
$db_handle = new DBController();

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}
$user_id = intval($_SESSION['user_id']);

// Fetch user info
$user = $db_handle->runQuery("SELECT * FROM all_users WHERE id = $user_id");
if ($user && count($user) > 0) {
    $user = $user[0];
} else {
    die("User not found.");
}

$success = $error = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['edit_profile'])) {
        $fields = [
            'name', 'email', 'number', 'address', 'age', 'sex', 'phone'];
        $updates = [];
        foreach ($fields as $field) {
            if (isset($_POST[$field])) {
                $val = mysqli_real_escape_string($db_handle->getConn(), $_POST[$field]);
                $updates[] = "$field = '$val'";
            }
        }
        // Handle profile image upload
        if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
            $img_name = basename($_FILES['profile_image']['name']);
            $target = '../images/' . $img_name;
            if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $target)) {
                $updates[] = "profile_image = '" . mysqli_real_escape_string($db_handle->getConn(), $img_name) . "'";
            }
        }
        if ($updates) {
            $sql = "UPDATE all_users SET ".implode(", ", $updates)." WHERE id = $user_id";
            mysqli_query($db_handle->getConn(), $sql);
            $success = "Profile updated successfully.";
            $user = $db_handle->runQuery("SELECT * FROM all_users WHERE id = $user_id")[0];
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Profile</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="profile.css" type="text/css" rel="stylesheet" />
</head>
<body>

<div class="profile-card">
    <img src="../images/<?php echo htmlspecialchars($user['profile_image']); ?>" alt="Profile Picture">
    <h2>
        <?php 
        //echo htmlspecialchars($user['name']); 
        ?>
</h2>
    <div class="profile-details">
        <strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?><br>
        <strong>Number:</strong> <?php echo htmlspecialchars($user['number']); ?><br>
        <strong>Address:</strong> <?php echo htmlspecialchars($user['address']); ?><br>
        <strong>Age:</strong> <?php echo htmlspecialchars($user['age']); ?><br>
        <strong>Sex:</strong> <?php echo htmlspecialchars($user['sex']); ?><br>
        <strong>Phone:</strong> <?php echo htmlspecialchars($user['phone']); ?><br>
        <strong>Type:</strong> <?php echo htmlspecialchars($user['type']); ?><br>
    </div>
    <button class="edit-btn" onclick="document.getElementById('edit-form').style.display='block';this.style.display='none';">Edit Profile</button>
    <?php if ($success) { echo '<div class="success">'.$success.'</div>'; } ?>
    <?php if ($error) { echo '<div class="error">'.$error.'</div>'; } ?>
    <form id="edit-form" class="edit-form" method="post" action="" enctype="multipart/form-data">
        
    <label for="profile_image">Profile Image:</label><br>
        <input type="file" id="profile_image" name="profile_image" accept="image/*"><br><br>

        <label for="name">Name:</label><br>
        <input type="text" id="name" name="name" value="
        <?php 
        //echo htmlspecialchars($user['name']); 
        ?>"
         required><br><br>

        <label for="email">Email:</label><br>
        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>"><br><br>

        <label for="number">Number:</label><br>
        <input type="text" id="number" name="number" value="<?php echo htmlspecialchars($user['number']); ?>"><br><br>


        <label for="change_password">Change Password:</label><br>
        <input type="password" id="change_password" name="change_password"value="
        <?php
        // echo htmlspecialchars($user['change_password']);
        
        ?>"><br><br>

        <label for="address">Address:</label><br>
        <textarea id="address" name="address" rows="3" cols="40"><?php echo htmlspecialchars($user['address']); ?></textarea><br><br>

        <label for="age">Age:</label><br>
        <input type="number" id="age" name="age" value="<?php echo htmlspecialchars($user['age']); ?>"><br><br>

        <label for="sex">Sex:</label><br>
        <input type="text" id="sex" name="sex" value="<?php echo htmlspecialchars($user['sex']); ?>"><br><br>

        <label for="phone">Phone:</label><br>
        <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($user['phone']); ?>"><br><br>

        

        <input type="submit" name="edit_profile" value="Save Changes">
    </form>
</div>
</body>
</html>
