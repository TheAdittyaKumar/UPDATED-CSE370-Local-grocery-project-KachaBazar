<?php
session_start();
if (!isset($_SESSION['admin_id']) || $_SESSION['role'] != 'admin') {
    header("Location: admin_login.php");
    exit();
}


$conn = mysqli_connect('localhost', 'root', '', 'kachabazarDB');
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Handle delete user
if (isset($_POST['delete_user'])) {
    $customer_id = $_POST['customer_id'];

    // Step 1: Find all order_ids of this user
    $orders_query = "SELECT order_id FROM `Order` WHERE UScustomer_id = $customer_id";
    $orders_result = mysqli_query($conn, $orders_query);

    $order_ids = [];
    while ($row = mysqli_fetch_assoc($orders_result)) {
        $order_ids[] = $row['order_id'];
    }

    // Step 2: Delete from Contain table
    if (!empty($order_ids)) {
        $order_ids_str = implode(',', $order_ids);
        mysqli_query($conn, "DELETE FROM Contain WHERE ORorder_id IN ($order_ids_str)");
    }

    // Step 3: Delete from Order table
    mysqli_query($conn, "DELETE FROM `Order` WHERE UScustomer_id = $customer_id");

    // Step 4: Finally delete from User table
    mysqli_query($conn, "DELETE FROM User WHERE customer_id = $customer_id");
}

// Fetch all non-admin users
$users = mysqli_query($conn, "SELECT * FROM User WHERE admin = 0");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Customer Registration</title>
</head>
<body>
    <h2>Register as a KachaBazar Customer!</h2>
    <h3>We believe customer is the king.</h3>
    <form action="" method="POST">
        <label>Name: </label>
        <input type="text" name="uname" required><br><br>
        <label>Phone Number: </label>
        <input type="text" name="phone" required><br><br>
        <label>Email: </label>
        <input type="email" name="email" required><br><br>
        <label>Password: </label>
        <input type="password" name="password" required><br><br>
        <input type="submit" value="Register">
    </form>
</body>
</html>