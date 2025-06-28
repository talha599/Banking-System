<?php
session_start();
if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

$bgColor = isset($_COOKIE['bg_color']) ? $_COOKIE['bg_color'] : '';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <style>
        body, html {
            margin: 0; padding: 0;
            height: 100%;
            font-family: Arial, sans-serif;
            font-size: 18px;
        }
        .background {
            background: <?= $bgColor ? htmlspecialchars($bgColor) : "url('bg.png') no-repeat center center/cover"; ?>;
            height: 100%;
            width: 100%;
            position: fixed;
            z-index: -1;
            opacity: 0.5;
        }
        .content {
            position: relative;
            z-index: 1;
            text-align: center;
            padding-top: 60px;
            color: #fff;
        }
         h2 {
        font-size: 30px;
        color: black; 
        }
         a {
        display: block;
        margin: 12px auto;
        padding: 14px 24px;
        width: 220px;
        font-size: 18px;
        background-color: #007bff;
        color: white;
        text-decoration: none;
        border-radius: 5px;
    }

    .color-picker {
        margin-top: 25px;
        font-size: 18px;
    }

    .color-picker label {
        color: black;
        font-weight: bold;
    }
    </style>
</head>
<body>
    <div class="background"></div>
    <div class="content">
        <h2>Welcome, <?= htmlspecialchars($_SESSION['name']); ?>!</h2><br>
        <a href="account.php">View Account</a>
        <a href="transfer.php">Transfer Money</a>
        <a href="paybills.php">Pay Bills</a><br>
        <div class="color-picker">
            <label for="bgColorPicker">Choose Background Color:</label>
            <input type="color" id="bgColorPicker" value="<?= $bgColor ?: '#ffffff' ?>">
        </div>
        <a href="logout.php" style="color:maroon; margin-top:20px;">Logout</a>
    </div>
    <script>
        const colorPicker = document.getElementById('bgColorPicker');
        colorPicker.addEventListener('change', function () {
            const selectedColor = this.value;
            document.querySelector('.background').style.background = selectedColor;
            document.cookie = `bg_color=${selectedColor}; path=/; max-age=${60 * 60 * 24 * 30}`;
        });
    </script>
</body>
</html>
