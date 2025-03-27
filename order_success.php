<?php
session_start();
include 'db_connection.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // Load PHPMailer

// ✅ Check if order ID is received
if (!isset($_GET['order_id'])) {
    die("❌ Invalid request.");
}

$order_id = $_GET['order_id'];

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

// ✅ Send Order Confirmation Email
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
    $mail->Subject = "Order Confirmation - Order #{$order_id}";
    $mail->Body = "
        <h3>🎉 Order Successfully Placed!</h3>
        <p>Your order ID: <strong>#{$order_id}</strong></p>
        <p>Thank you for your order. Your food is being prepared! 🍕</p>
        <p>We will notify you when it is out for delivery.</p>
    ";

    $mail->send();
    $email_status = "✅ Order confirmation email sent!";
} catch (Exception $e) {
    $email_status = "✅ Order placed, but email could not be sent. Error: {$mail->ErrorInfo}";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Success</title>
    <style>
        body { font-family: Arial, sans-serif; text-align: center; padding: 20px; }
        .container { max-width: 400px; margin: auto; border: 1px solid #ddd; padding: 20px; border-radius: 10px; box-shadow: 0px 0px 10px rgba(0,0,0,0.1); }
        .btn { display: inline-block; padding: 10px 20px; background-color: #28a745; color: white; text-decoration: none; border-radius: 5px; margin-top: 10px; }
        .btn:hover { background-color: #218838; }
    </style>
</head>
<body>

<div class="container">
    <h2>✅ Order Placed Successfully!</h2>
    <p><strong>Order ID:</strong> #<?php echo $order_id; ?></p>
    <p>Your food is being prepared. 🚀</p>
    <p>We will notify you once it's out for delivery. 🍕</p>
    
    <p><?php echo $email_status; ?></p>

    <a href="index.php" class="btn">Return to Home</a>
</div>

</body>
</html>
