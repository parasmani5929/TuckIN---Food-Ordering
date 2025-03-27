<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    die("❌ Access denied. <a href='adminlogin.html'>Admin Login</a>");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f9;
            color: #333;
            margin: 0;
            padding: 0;
        }

        h1 {
            text-align: center;
            margin-top: 20px;
            color: #4CAF50;
        }

        p {
            text-align: center;
            font-size: 1.1em;
            color: #555;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin-top: 30px;
            text-align: center; /* Centered content */
        }

        .admin-name {
            font-weight: bold;
            color: #4CAF50;
        }

        ul {
            list-style-type: none;
            padding: 0;
            margin-top: 30px;
        }

        li {
            margin: 15px 0; /* Spacing between the buttons */
        }

        li a {
            display: inline-block;
            text-align: center;
            padding: 12px 30px; /* Consistent button size */
            font-size: 1.1em;
            color: white;
            background-color: #007bff; /* Default button color */
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s, transform 0.2s;
            min-width: 200px; /* Ensures buttons are all the same width */
        }

        li a:hover {
            background-color: #0056b3;
            transform: scale(1.05);
        }

        li a:active {
            background-color: #004085;
        }

        li a.logout {
            background-color: #dc3545; /* Change color for logout button */
        }

        li a.logout:hover {
            background-color: #c82333; /* Darker red on hover */
        }

        footer {
            text-align: center;
            margin-top: 40px;
            font-size: 0.9em;
            color: #777;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Welcome Admin, <span class="admin-name"><?php echo $_SESSION['admin_name']; ?></span>!</h1>
    <p>This is the admin dashboard. It is only accessible to Paras, Rasha, Surya, and Saurav.</p>

    <ul>
        <li><a href="manage_orders.php">Manage Orders</a></li>
        <li><a href="manage_food.php">Manage Food Items</a></li>
        <li><a href="logout.php" class="logout">Logout</a></li>
    </ul>
</div>

<footer>
    <p>© 2025 Admin Dashboard</p>
</footer>

</body>
</html>
