<?php
session_start();
include 'db_connection.php';

// ✅ Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    die("❌ You must be logged in to track your order. <a href='login.html'>Login here</a>");
}

// ✅ Fetch the latest order for the logged-in user
$stmt = $pdo->prepare("SELECT order_id, status FROM orders WHERE user_id = ? ORDER BY order_date DESC LIMIT 1");
$stmt->execute([$_SESSION['user_id']]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

// ✅ If no order found, show a message
if (!$order) {
    die("🛒 You have not placed any orders yet. <a href='index.php'>Order Now</a>");
}

// ✅ Define tracking stages
$stages = ["Preparing", "On the Way", "Out for Delivery", "Delivered"];
$current_stage = array_search($order['status'], $stages);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Track Your Order</title>
    <style>
        body { font-family: Arial, sans-serif; text-align: center; padding: 20px; }
        .tracker { display: flex; justify-content: space-between; max-width: 600px; margin: 20px auto; }
        .step { padding: 10px; width: 24%; background: #ddd; border-radius: 5px; }
        .active { background: #28a745; color: white; }
    </style>
</head>
<body>

<h2>📦 Track Your Order</h2>
<p>Order ID: <strong>#<?php echo $order['order_id']; ?></strong></p>

<div class="tracker">
    <?php foreach ($stages as $index => $stage): ?>
        <div class="step <?php echo $index <= $current_stage ? 'active' : ''; ?>">
            <?php echo $stage; ?>
        </div>
    <?php endforeach; ?>
</div>

<p>Current Status: <strong><?php echo $order['status']; ?></strong></p>

</body>
</html>
