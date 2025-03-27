<?php
session_start();
include 'db_connection.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // Load PHPMailer

// ✅ Check if order ID is received
if (!isset($_POST['order_id']) || !isset($_POST['amount'])) {
    die("❌ Invalid request.");
}

$order_id = $_POST['order_id'];
$amount = $_POST['amount'];

// ✅ Fetch user email from the database
$stmt = $pdo->prepare("SELECT users.email FROM users 
                       JOIN orders ON users.user_id = orders.user_id 
                       WHERE orders.order_id = ?");
$stmt->execute([$order_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    die("❌ User not found.");
}

$user_email = $user['email'];

// ✅ Update order status to "Paid"
$stmt = $pdo->prepare("UPDATE orders SET status = 'Paid' WHERE order_id = ?");
$stmt->execute([$order_id]);

// ✅ Send Payment Confirmation Email
$mail = new PHPMailer(true);
try {
    // ✅ SMTP settings
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'admin@gmail.com'; // Admin email
    $mail->Password = 'xxxx xxxx xxxx xxxx'; // Gmail App Password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;

    // ✅ Email details
    $mail->setFrom('admin@gmail.com', 'Tuckin Food Order');
    $mail->addAddress($user_email);

    // ✅ Email content
    $mail->isHTML(true);
    $mail->Subject = "Payment Confirmation - Order #{$order_id}";
    $mail->Body = "
        <h3>🎉 Payment Successful!</h3>
        <p>Your order ID: <strong>#{$order_id}</strong></p>
        <p>Total Amount Paid: <strong>₹{$amount}</strong></p>
        <p>Thank you for your order. Your food is being prepared! 🍕</p>
    ";

    $mail->send();
    echo "✅ Payment confirmed! A confirmation email has been sent.";
    //header("Location: index.php");
} catch (Exception $e) {
    echo "✅ Payment confirmed, but email could not be sent. Error: {$mail->ErrorInfo}";
}

?>
