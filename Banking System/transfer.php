<?php
session_start();
if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

$message = '';
$bgColor = isset($_COOKIE['bg_color']) ? $_COOKIE['bg_color'] : '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $conn = mysqli_connect("localhost", "root", "", "banking");
    $from = $_SESSION['email'];
    $to = $_POST['to_email'];
    $amount = $_POST['amount'];

    $check_sender = mysqli_query($conn, "SELECT balance FROM users WHERE email='$from'");
    $sender = mysqli_fetch_assoc($check_sender);

    if ($sender['balance'] >= $amount) {
        mysqli_query($conn, "UPDATE users SET balance = balance - $amount WHERE email='$from'");
        mysqli_query($conn, "UPDATE users SET balance = balance + $amount WHERE email='$to'");
        mysqli_query($conn, "INSERT INTO transactions (from_user, to_user, amount, date) VALUES ('$from', '$to', $amount, NOW())");
        $message = "Money transferred successfully.";
    } else {
        $message = "Not enough money.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Transfer Money</title>
    <style>
        body, html {
            margin: 0; padding: 0; height: 100%;
            font-family: Arial, sans-serif;
        }
        .background {
            background: <?= $bgColor ? htmlspecialchars($bgColor) : "url('bg.png') no-repeat center center/cover"; ?>;
            position: fixed;
            width: 100%;
            height: 100%;
            z-index: -1;
            opacity: 0.5;
        }
        .message-box {
            position: fixed;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            background: #fff;
            color: #000;
            padding: 10px 20px;
            border-radius: 5px;
            font-weight: bold;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.3);
            z-index: 9999;
        }
        .content {
            position: relative;
            z-index: 1;
            text-align: center;
            padding-top: 80px;
            color: #fff;
        }
        h2 {
            text-shadow: 2px 2px 4px #000;
            font-size: 30px;
        }
        form {
            background: rgba(0, 0, 0, 0.6);
            padding: 30px 40px;
            border-radius: 10px;
            display: inline-block;
        }
        input, button {
            width: 250px;
            margin: 10px auto;
            padding: 10px;
            font-size: 18px;
            border: none;
            border-radius: 5px;
        }
        button {
            background-color: #007bff;
            color: white;
            font-size: 18px;
        }
        button:hover {
            background-color: #0056b3;
        }
        .back-link {
            margin-top: 20px;
            display: block;
            color: white;
            text-decoration: none;
            font-weight: bold;
        }
        .back-link:hover {
            text-decoration: underline;
            color: red;
        }
    </style>
</head>
<body>
    <div class="background"></div>

    <?php if ($message): ?>
        <div class="message-box">
            <?= htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>

    <div class="content">
        <h2>Transfer Money</h2>
        <form method="POST">
            <input type="email" name="to_email" placeholder="Recipient Email" required><br>
            <input type="number" name="amount" placeholder="Amount" required><br>
            <button type="submit">Send</button>
            <a href="dashboard.php" class="back-link">Back to Dashboard</a>
        </form>
    </div>
</body>
</html>
