<?php
session_start();
include 'db_connection.php';

// ✅ Ensure admin is logged in
if (!isset($_SESSION['admin_id'])) {
    die("❌ Access denied. <a href='adminlogin.php'>Admin Login</a>");
}

// ✅ Fetch Orders with User & Food Details
$stmt = $pdo->query("
    SELECT o.order_id, o.user_id, o.total_price, o.order_date, o.status, u.name AS user_name, 
           GROUP_CONCAT(f.name SEPARATOR ', ') AS food_names, SUM(oi.quantity) AS total_quantity
    FROM orders o
    JOIN users u ON o.user_id = u.user_id
    JOIN order_items oi ON o.order_id = oi.order_id
    JOIN food_items f ON oi.food_id = f.food_id
    GROUP BY o.order_id
    ORDER BY o.order_date DESC
");
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Orders</title>
    <style>
        table {
            width: 85%;
            margin: 5;
            color: white;
        }
        body {
            background-image: url("back.jpg");
            background-size: 80%;
        }
    </style>
</head>
<body>

<h1>Manage Orders</h1>
<table border="5">
    <tr>
        <th>Order ID</th>
        <th>User</th>
        <th>Food Items</th>
        <th>Total Quantity</th>
        <th>Total Price</th>
        <th>Status</th>
        <th>Action</th>
    </tr>
    <?php foreach ($orders as $order): ?>
        <tr>
            <td><?php echo $order['order_id']; ?></td>
            <td><?php echo $order['user_name']; ?></td>
            <td><?php echo $order['food_names']; ?></td>
            <td><?php echo $order['total_quantity']; ?></td>
            <td>₹<?php echo $order['total_price']; ?></td>
            <td><?php echo $order['status']; ?></td>
            <td>
                <form action="update_order_status.php" method="POST">
                    <input type="hidden" name="order_id" value="<?php echo $order['order_id']; ?>">
                    <select name="new_status">
                        <option value="Pending" <?php if ($order['status'] == "Pending") echo "selected"; ?>>Pending</option>
                        <option value="Preparing" <?php if ($order['status'] == "Preparing") echo "selected"; ?>>Preparing</option>
                        <option value="On the Way" <?php if ($order['status'] == "On the Way") echo "selected"; ?>>On the Way</option>
                        <option value="Out for Delivery" <?php if ($order['status'] == "Out for Delivery") echo "selected"; ?>>Out for Delivery</option>
                        <option value="Delivered" <?php if ($order['status'] == "Delivered") echo "selected"; ?>>Delivered</option>
                    </select>
                    <button type="submit">Update</button>
                </form>
            </td>
        </tr>
    <?php endforeach; ?>
</table>

<a href="admin_dashboard.php">Back to Dashboard</a>

</body>
</html>
