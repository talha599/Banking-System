<?php
session_start();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $conn = mysqli_connect("localhost", "root", "", "banking");
    $email = $_POST['email'];
    $password = $_POST['password'];

    $res = mysqli_query($conn, "SELECT * FROM managers WHERE email='$email'");
    $mgr = mysqli_fetch_assoc($res);

    if ($mgr && $mgr['password'] === $password) {
        $_SESSION['manager_email'] = $mgr['email'];
        $_SESSION['manager_name'] = $mgr['name'];
        header("Location: manager_dashboard.php");
        exit();
    } else {
        echo "Invalid credentials.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manager Login</title>
    <style>
        body, html {
            margin: 0;
            padding: 0;
            height: 100%;
            font-family: Arial, sans-serif;
        }

        .background {
            position: fixed;
            top: 0; left: 0;
            width: 100%;
            height: 100%;
            background-image: url('bg.png'); 
            background-size: cover;
            background-position: center;
            opacity: 0.5;
            z-index: -1;
        }
        
        .content {
            position: relative;
            z-index: 1;
            text-align: center;
            padding-top: 100px;
        }

        form {
    display: inline-block;
    background: #fff;
    padding: 40px;
    border-radius: 10px;
    box-shadow: 0 0 10px rgba(0,0,0,0.2);
    width: 300px; 
    max-width: 90%;
}

    h2 {
        font-size: 25px;
    }
        input {
            display: block;
            margin: 10px auto;
            padding: 10px;
            width: 80%;
            max-width: 300px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 18px;
        }

        button {
            padding: 10px 20px;
            background: rgb(23, 55, 135);
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            width: 50%;
            font-size: 18px;
        }

        button:hover {
            background: rgb(73, 149, 221);
        }

       .back-link {
    display: inline-block;
    margin-top: 15px;
    padding: 10px 20px;
    background-color:hsl(138, 62.20%, 43.50%); 
    color: white;
    text-decoration: none;
    border-radius: 5px;
    transition: background-color 0.3s ease;
    font-size: 18px;
}

.back-link:hover {
    background-color: #146c43; 
}

    </style>
</head>
<body>

<div class="background"></div>

<div class="content">
    <form method="POST">
        <h2>Manager Login</h2>
        <input type="email" name="email" placeholder="Manager Email" required><br>
        <input type="password" name="password" placeholder="Password" required><br>
        <button type="submit">Login</button><br>
        <a class="back-link" href="index.html">Go to Homepage</a>
    </form>
</div>

</body>
</html>
