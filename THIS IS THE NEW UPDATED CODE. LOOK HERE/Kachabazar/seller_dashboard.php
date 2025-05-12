<?php
session_start();
if (!isset($_SESSION['seller_id']) || $_SESSION['role'] != 'seller') {
    header("Location: login.php"); // not seller go back to login zooooooom
    exit();
}
// Database connector zoooom
$conn = mysqli_connect('localhost', 'root', '', 'kachabazarDB');
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
// Get info regarding our current logged in seller
$seller_id = $_SESSION['seller_id']; //PK
$seller_query = "SELECT store_name, store_location, store_description FROM Seller WHERE seller_id = $seller_id";
$seller_result = mysqli_query($conn, $seller_query);
$seller = mysqli_fetch_assoc($seller_result);
// Get the reviews from Ratings&Review for this specific seller using SEseller_id FK to link to Seller.seller_id
$review_query = "SELECT * FROM `Ratings&Review` WHERE SEseller_id = $seller_id ORDER BY Rdate_time DESC";
$review_result = mysqli_query($conn, $review_query);
// Calculate average rating
$avg_query = "SELECT AVG(rating) AS average_rating FROM `Ratings&Review` WHERE SEseller_id = $seller_id";
$avg_result = mysqli_query($conn, $avg_query);
$avg_row = mysqli_fetch_assoc($avg_result);
$average_rating = $avg_row['average_rating'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Seller Dashboard</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body class="dashboard-page">
<div class="dashboard-container">
    <div class="sidebar">
        <h2>Menu</h2>
        <ul>
            <li><a href="manage_products.php">Manage Products</a></li>
            <li><a href="view_orders_seller.php">View Orders</a></li>
            <li><a href="update_store_info.php">Update Store Info</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </div>
    <div class="main-content">
    <div class="fixed-width-wrapper"> 
        <h1>Welcome, <?php echo htmlspecialchars($_SESSION['seller_name']); ?>!</h1>
        <div class="store-info box seller-details-box">
            <div class="seller-info">
                <h2>Your Store Details:</h2>
                <p><strong>Store Name:</strong> <?php echo htmlspecialchars($seller['store_name']); ?></p>
                <p><strong>Store Location:</strong> <?php echo htmlspecialchars($seller['store_location']); ?></p>
                <p><strong>Store Description:</strong> <?php echo htmlspecialchars($seller['store_description']); ?></p>
            </div>
        </div>
        <?php if ($seller_id == 3): ?> <!-- Cover only in don no.1 store -->
            <div class="store-cover">
                <img src="cover_seller_store_1.png" alt="Store Cover">
            </div>
        <?php endif; ?>
        <div class="rating-info box">  <!-- Rating ***** -->
            <h2>Overall Customer Rating:</h2>
            <?php
            if ($average_rating) { //round to 1 decimal point like 4.3
                echo "<p><span class='rating-star'>★</span> " . round($average_rating, 1) . "/5</p>";
            } else {
                echo "<p>No ratings yet</p>"; //no rating then display this
            }
            ?>
        </div>
        <div class="reviews-info box"> <!-- Reviews are here -->
            <h2>Customer Reviews:</h2>
            <table>
                <thead>
                    <tr>
                        <th>Review ID</th>
                        <th>Rating</th>
                        <th>Review</th>
                        <th>Date & Time</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (mysqli_num_rows($review_result) > 0) {
                        while ($review = mysqli_fetch_assoc($review_result)) {
                            echo "<tr>";
                            echo "<td>{$review['review_id']}</td>";
                            echo "<td>{$review['rating']}</td>";
                            echo "<td>" . htmlspecialchars($review['review']) . "</td>";
                            echo "<td>{$review['Rdate_time']}</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='4'>No reviews available yet!</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>

    </div> <!-- End of fixed width wrapper -->
</div>

</body>
</html>
