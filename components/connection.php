 <?php
 $servername = "localhost";
 $username = "root"; // Default XAMPP username
 $password = ""; // Default XAMPP password (empty)
 $dbname = "cafe_db";
 // Create connection
 $conn = new mysqli($servername, $username, $password, $dbname);
 // Check connection
 if ($conn->connect_error) {
 die("Connection failed: " . $conn->connect_error);
 }
//  echo "Connected successfully";
 // Close connection
//  $conn->close();
 ?>