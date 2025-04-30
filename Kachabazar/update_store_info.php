<?php
session_start();

if (!isset($_SESSION['seller_id']) || $_SESSION['role'] != 'seller') {
    header("Location: login.php");
    exit();
}

// Database connection
$conn = mysqli_connect('localhost', 'root', '', 'kachabazarDB');
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Fetch the seller's current store information
$seller_id = $_SESSION['seller_id'];
$seller_query = "SELECT store_name, store_location, store_description FROM Seller WHERE seller_id = $seller_id";
$seller_result = mysqli_query($conn, $seller_query);

// Set default values
$store_name = '';
$store_location = '';
$store_description = '';

if ($seller_row = mysqli_fetch_assoc($seller_result)) {
    $store_name = $seller_row['store_name'];
    $store_location = $seller_row['store_location'];
    $store_description = $seller_row['store_description'];
}

// Handle form submission to update store info
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_store'])) {
    $new_store_name = mysqli_real_escape_string($conn, $_POST['store_name']);
    $new_store_location = mysqli_real_escape_string($conn, $_POST['store_location']);
    $new_store_description = mysqli_real_escape_string($conn, $_POST['store_description']);

    $update_query = "UPDATE Seller SET 
                        store_name = '$new_store_name',
                        store_location = '$new_store_location',
                        store_description = '$new_store_description'
                     WHERE seller_id = $seller_id";

    if (mysqli_query($conn, $update_query)) {
        header("Location: seller_dashboard.php"); // After successful update, go back to dashboard
        exit();
    } else {
        echo "Error updating store information.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Update Store Information</title>
    <link rel="stylesheet" href="css/styles.css"> <!-- Your existing CSS -->
</head>
<body>

<div class="form-container">
    <h2>Update Your Store Information</h2>

    <form method="POST" action="update_store_info.php">
        <label>Store Name:</label>
        <input type="text" name="store_name" value="<?php echo htmlspecialchars($store_name); ?>" required>

        <label>Store Location:</label>
        <input type="text" name="store_location" value="<?php echo htmlspecialchars($store_location); ?>" required>

        <label>Store Description:</label>
        <textarea name="store_description" rows="5" required><?php echo htmlspecialchars($store_description); ?></textarea>

        <input type="submit" name="update_store" value="Update Store" class="action-button">
    </form>

    <div class="manage-buttons">
        <a href="seller_dashboard.php" class="action-button">Back to Dashboard</a>
        <a href="logout.php" class="action-button logout-button">Logout</a>
    </div>
</div>

</body>
</html>
