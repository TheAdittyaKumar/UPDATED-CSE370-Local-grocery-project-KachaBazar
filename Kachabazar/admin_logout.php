<?php
session_start(); //Start session
session_unset(); 
session_destroy(); 
header("Location: admin_login.php"); //redicter
exit();
?>
