<?php
if (isset($message)) {
   foreach ($message as $message) {
      echo '
      <div class="message">
         <span>' . $message . '</span>
         <i class="fas fa-times" onclick="this.parentElement.remove();"></i>
      </div>
      ';
   }
}
?>

<header class="header">

    <section class="flex">

        <a href="dashboard.php" class="logo">Admin Panel</a>

        <nav class="navbar">
            <a href="dashboard.php">Home</a>
            <a href="admin_accounts.php">Admins</a>
            <a href="employee_accounts.php">Employees</a>
            <a href="users_accounts.php">Users</a>
            <a href="products.php">Products</a>
            <a href="placed_orders.php">Orders</a>
            <a href="messages.php">Messages</a>
        </nav>

        <div class="icons">
            <div id="menu-btn" class="fas fa-bars"></div>
            <div id="user-btn" class="fas fa-user"></div>
        </div>

        <div class="profile">
            <?php
         $select_profile = $conn->prepare("SELECT * FROM `all_users` WHERE id = ?");
         $select_profile->execute([$admin_id]);
         $fetch_profile = $select_profile->fetch(PDO::FETCH_ASSOC);
         ?>
            <p><?= $fetch_profile['name']; ?></p>
            <a href="update_profile.php" class="btn">update profile</a>
            <a href="../components/admin_logout.php" onclick="return confirm('logout from this website?');"
                class="delete-btn">logout</a>
        </div>

    </section>

</header>