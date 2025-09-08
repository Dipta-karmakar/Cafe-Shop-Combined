<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <h1>Welcome,
        <?php echo $_SESSION["username"];?> </h1>


    <form action="" method="post">

        <input type="submit" name="LogOut" value=" LogOut">


    </form>

</body>

</html>
<?php

if(isset($_SERVER["REQUEST_METHOD"])=="POST"){

        if(isset($_POST["LogOut"])){


            if(isset($_POST["LogOut"])){
                header("Location:login.php");
            exit();

                }
        }}
         else{
            header("Location:login.php");

        }

?>