<?php 
session_start();
include 'db_connection.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // Load PHPMailer

// ✅ Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    die("❌ You must be logged in to place an order. <a href='login.html'>Login here</a>");
}

// ✅ Ensure cart is not empty
if (!isset($_SESSION["cart"]) || empty($_SESSION["cart"])) {
    die("🛒 Your cart is empty. <a href='index.php'>Go back to menu</a>");
}

// ✅ Fetch user's email from the database
$stmt = $pdo->prepare("SELECT email FROM users WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$user) {
    die("❌ Error: User not found.");
}

$user_email = $user['email']; // ✅ User's email

// ✅ Calculate Total Price
$total_price = 0;
foreach ($_SESSION["cart"] as $food_id => $quantity) {
    $stmt = $pdo->prepare("SELECT price FROM food_items WHERE food_id = ?");
    $stmt->execute([$food_id]);
    $food = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($food) {
        $total_price += $food["price"] * $quantity;
    }
}
?>

<!-- ✅ Checkout Form -->
<form action="checkout.php" method="POST">
    <h3>Select Payment Method:</h3>
    
    <div class="payment-options">
        <!-- ✅ UPI Payment Button -->
        <button type="submit" name="payment_method" value="upi" class="payment-btn upi-btn">
            Pay Now (UPI QR Code)
        </button>

        <!-- ✅ Cash on Delivery Button -->
        <button type="submit" name="payment_method" value="cod" class="payment-btn cod-btn">
            Cash on Delivery
        </button>
    </div>
</form>

<!-- ✅ Styles for Better UI of checkout form -->
<style>
    body { font-family: Arial, sans-serif; text-align: center; padding: 20px; }
    h3 { margin-bottom: 15px; font-size: 20px; }
    .payment-options { display: flex; justify-content: center; gap: 20px; }
    .payment-btn {
        border: none;
        padding: 15px 20px;
        font-size: 16px;
        cursor: pointer;
        border-radius: 8px;
        transition: 0.3s;
        width: 180px;
    }
    .upi-btn { background-color: #007bff; color: white; }
    .upi-btn:hover { background-color: #0056b3; }
    .cod-btn { background-color: #28a745; color: white; }
    .cod-btn:hover { background-color: #1e7e34; }
</style>

<?php
// ✅ Check if payment method is selected
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['payment_method'])) {
        die("❌ Please select a payment method. <a href='checkout.php'>Go back</a>");
    }

    $payment_method = $_POST['payment_method'];

    // ✅ Insert Order into `orders` table
    try {
        $pdo->beginTransaction();

        // ✅ Insert order
        $stmt = $pdo->prepare("INSERT INTO orders (user_id, total_price, order_date, status) VALUES (?, ?, NOW(), 'Pending')");
        $stmt->execute([$_SESSION["user_id"], $total_price]);
        $order_id = $pdo->lastInsertId(); // ✅ Get the auto-generated `order_id`

        // ✅ Insert food items into `order_items`
        foreach ($_SESSION["cart"] as $food_id => $quantity) {
            $stmt = $pdo->prepare("SELECT price FROM food_items WHERE food_id = ?");
            $stmt->execute([$food_id]);
            $food = $stmt->fetch(PDO::FETCH_ASSOC);

            $stmt = $pdo->prepare("INSERT INTO order_items (order_id, food_id, quantity, price) VALUES (?, ?, ?, ?)");
            $stmt->execute([$order_id, $food_id, $quantity, $food["price"]]);
        }

        $pdo->commit();

        // ✅ Clear cart after order placement
        unset($_SESSION["cart"]);

        // ✅ Redirect based on payment method
        if ($payment_method === 'upi') {
            header("Location: payment.php?order_id=$order_id&amount=$total_price");
        } else {
            header("Location: order_success.php?order_id=$order_id");
        }
        exit();

    } catch (PDOException $e) {
        $pdo->rollBack();
        die("❌ Error processing order: " . $e->getMessage());
    }
}
?>
