<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $conn = mysqli_connect("localhost", "root", "", "banking");
    if (!$conn) die("Connection failed: " . mysqli_connect_error());

    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $check = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");
    if (mysqli_num_rows($check) > 0) {
        echo "Email already registered.";
    } else {
        mysqli_query($conn, "INSERT INTO users (name, email, password, balance) VALUES ('$name', '$email', '$password', 0)");
        header("Location: login.php");
    }
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
    <link rel="stylesheet" href="css/style.css">
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
            color: #fff;
            text-shadow: 2px 2px 4px #000;
        }

        form {
            background: rgba(0, 0, 0, 0.6);
            display: inline-block;
            padding: 30px 40px;
            border-radius: 10px;
        }

        input {
            display: block;
            width: 250px;
            margin: 10px auto;
            padding: 10px;
            border: none;
            border-radius: 5px;
            font-size: 18px;
        }

        button {
            padding: 10px 20px;
            border: none;
            background-color: #007bff;
            color: #fff;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
        }

        button:hover {
            background-color: #0056b3;
        }

        .dashboard-link {
            margin-top: 15px;
            display: block;
            color:rgb(250, 107, 107);
            font-size: 18px;
            text-decoration: none;
        }

        .dashboard-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="background"></div>

    <div class="content">
        <h2>Register</h2>
        <form method="POST">
            <input type="text" name="name" placeholder="Full Name" required><br>
            <input type="email" name="email" placeholder="Email" required><br>
            <input type="password" name="password" placeholder="Password" required><br>
            <button type="submit">Register</button>
            <a class="dashboard-link" href="index.html">Go to Homepage</a>
        </form>
    </div>
</body>
</html>
