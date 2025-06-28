<?php
session_start();
if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

$conn = mysqli_connect("localhost", "root", "", "banking");
$email = $_SESSION['email'];
$result = mysqli_query($conn, "SELECT balance FROM users WHERE email='$email'");
$row = mysqli_fetch_assoc($result);

$bgColor = isset($_COOKIE['bg_color']) ? $_COOKIE['bg_color'] : '';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Account</title>
    <style>
        body, html {
            margin: 0;
            padding: 0;
            height: 100%;
            font-family: Arial, sans-serif;
            font-size: 18px;
        }
        .background {
            background: <?= $bgColor ? htmlspecialchars($bgColor) : "url('bg.png') no-repeat center center/cover"; ?>;
            position: fixed;
            width: 100%;
            height: 100%;
            z-index: -1;
            opacity: 0.5;
        }
        .content {
            position: relative;
            z-index: 1;
            text-align: center;
            padding-top: 100px;
            color: #fff;
        }
        h2 {
            font-size: 28px;
            color: black;
            text-shadow: none;
            
        }
        a {
            display: inline-block;
            margin-top: 20px;
            padding: 14px 24px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-size: 18px;
        }
        a:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="background"></div>
    <div class="content">
        <h2>Your Balance: $<?= $row['balance']; ?></h2>
        <a href="dashboard.php">Back to Dashboard</a>
    </div>
</body>
</html>
