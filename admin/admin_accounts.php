<?php
include '../components/connect.php';
session_start();

$admin_id = $_SESSION['user_id'];
if (!isset($admin_id)) {
   header('location: ../login.php');
   exit;
}

// if (isset($_GET['delete'])) {
//    $delete_id = $_GET['delete'];
//    $delete_admin = $conn->prepare("DELETE FROM `all_users` WHERE id = ?");
//    $delete_admin->execute([$delete_id]);
//    header('location:admin_accounts.php');
//    exit;
// }


//modified so admin dont accidently delete himself
if (isset($_GET['delete'])) {
    $delete_id = $_GET['delete'];

    if ($delete_id != $admin_id) {  // Prevent self-deletion
        $delete_admin = $conn->prepare("DELETE FROM `all_users` WHERE id = ?");
        $delete_admin->execute([$delete_id]);
        header('location:admin_accounts.php');
        exit;
    } else {
        // Optional: show error message
        $message[] = "You cannot delete your own account!";
    }
}


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admins Accounts</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link rel="stylesheet" href="../css/dashboard_style.css">
    <link rel="stylesheet" href="../css/table.css">
</head>

<body>
    <?php include '../components/admin_header.php'; ?>

    <section class="accounts">
        <h1 class="heading">Admin Management</h1>

        <div class="table_header">
            <p>Admin Details</p>
            <div>
                <a href="register_admin.php"><button class="add_new">Add Admin</button></a>
            </div>
        </div>

        <div>
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Profile</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Number</th>
                        <th>Phone</th>
                        <th>Age</th>
                        <th>Sex</th>
                        <th>Address</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $select_account = $conn->prepare("SELECT * FROM `all_users` WHERE type='admin'");
                    $select_account->execute();
                    if ($select_account->rowCount() > 0) {
                        while ($fetch_accounts = $select_account->fetch(PDO::FETCH_ASSOC)) {
                            $profile_img = !empty($fetch_accounts['profile_image']) ? $fetch_accounts['profile_image'] : 'default.png';
                    ?>
                    <tr>
                        <td><?= $fetch_accounts['id']; ?></td>
                        <td>
                            <img src="../uploaded_img/<?= htmlspecialchars($profile_img); ?>" alt="profile"
                                style="width:50px;height:50px;border-radius:50%;">
                        </td>
                        <td><?= htmlspecialchars($fetch_accounts['username']); ?></td>
                        <td><?= htmlspecialchars($fetch_accounts['email']); ?></td>
                        <td><?= htmlspecialchars($fetch_accounts['number']); ?></td>
                        <td><?= htmlspecialchars($fetch_accounts['phone']); ?></td>
                        <td><?= htmlspecialchars($fetch_accounts['age']); ?></td>
                        <td><?= htmlspecialchars($fetch_accounts['sex']); ?></td>
                        <td><?= htmlspecialchars($fetch_accounts['address']); ?></td>
                        <td>
                            <?php if ($fetch_accounts['id'] == $admin_id): ?>
                            <!-- Only the logged-in admin can edit their own profile -->
                            <a href="update_profile.php?id=<?= $fetch_accounts['id']; ?>">
                                <button><i class="fa-solid fa-pen-to-square"></i></button>
                            </a>
                            <?php else: ?>
                            <!-- Logged-in admin can delete other admins -->
                            <a href="admin_accounts.php?delete=<?= $fetch_accounts['id']; ?>"
                                onclick="return confirm('Delete this account?');">
                                <button><i class="fa-solid fa-trash"></i></button>
                            </a>
                            <?php endif; ?>

                            <!-- <a href="admin_accounts.php?delete=<?= $fetch_accounts['id']; ?>"
                                onclick="return confirm('Delete this account?');">
                                <button><i class="fa-solid fa-trash"></i></button>
                            </a> -->
                        </td>
                    </tr>
                    <?php
                        }
                    } else {
                        echo '<tr><td colspan="10" class="empty">No admin accounts available</td></tr>';
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </section>

    <script src="../js/admin_script.js"></script>
</body>

</html>