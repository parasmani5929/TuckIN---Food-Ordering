<?php
session_start();
include 'db_connection.php';

// ✅ Ensure admin is logged in
if (!isset($_SESSION['admin_id'])) {
    die("❌ Access denied. <a href='adminlogin.php'>Admin Login</a>");
}

// ✅ Ensure required data is received
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["order_id"], $_POST["new_status"])) {
    $order_id = $_POST["order_id"];
    $new_status = $_POST["new_status"];

    // ✅ Update order status in database
    $stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE order_id = ?");
    if ($stmt->execute([$new_status, $order_id])) {
        echo "✅ Order status updated successfully!";
    } else {
        echo "❌ Failed to update order status.";
    }
}

// ✅ Redirect back to manage orders page
header("Location: manage_orders.php");
exit;
?>
