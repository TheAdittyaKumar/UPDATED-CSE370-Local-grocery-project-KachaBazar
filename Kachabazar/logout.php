<?php
session_start();

// Save role before session is destroyed
$role = $_SESSION['role'] ?? null;

// Destroy session
session_unset();
session_destroy();

// Redirect based on saved role
if ($role === 'admin') {
    header("Location: admin_login.php");
} else {
    header("Location: login.php"); // default for customers/sellers
}
exit();
