<?php
session_start();
if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $conn = mysqli_connect("localhost", "root", "", "banking");
    if (!$conn) die("Connection failed.");

    $email = $_SESSION['email'];
    $amount = floatval($_POST['amount']);

    if ($amount > 0) {
        mysqli_query($conn, "UPDATE users SET balance = balance + $amount WHERE email='$email'");
        mysqli_query($conn, "INSERT INTO transactions (from_user, to_user, amount, date) VALUES ('SELF', '$email', $amount, NOW())");
        echo "<script>alert('Money added successfully!');</script>";
    } else {
        echo "<script>alert('Enter a valid amount.');</script>";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Add Money</title>
    <style>
        body, html {
            margin: 0;
            padding: 0;
            height: 100%;
            font-family: Arial, sans-serif;
        }

        .background {
            background-image: url('bg.png');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
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
            color: #fff;
            text-shadow: 2px 2px 4px #000;
        }

        form {
            display: inline-block;
            background: rgba(0,0,0,0.6);
            padding: 20px 30px;
            border-radius: 10px;
        }

        input {
            display: block;
            width: 250px;
            margin: 10px auto;
            padding: 10px;
            border: none;
            border-radius: 5px;
        }

        button {
            padding: 10px 20px;
            background-color:rgb(52, 94, 199);
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
        }

        button:hover {
            background-color:rgb(27, 49, 105);
        }

        a {
            display: block;
            margin-top: 15px;
            color: #000;
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="background"></div>
    <div class="content">
        <h2>Add Money to Your Account</h2>
        <form method="POST">
            <input type="number" name="amount" placeholder="Amount" step="0.01" required><br>
            <button type="submit">Add Money</button>
        </form>
        <a href="dashboard.php">Back to Dashboard</a>
    </div>
</body>
</html>
