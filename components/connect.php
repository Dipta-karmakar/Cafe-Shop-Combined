<?php
// local host
$db_name = 'mysql:host=localhost;dbname=cafe_db';
$user_name = 'root';
$user_password = '';

// connection variable
$conn = new PDO($db_name, $user_name, $user_password);