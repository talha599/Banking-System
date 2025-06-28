<?php
session_start();
if (!isset($_SESSION['manager_email'])) {
    header("Location: login_manager.php");
    exit();
}

$conn = mysqli_connect("localhost", "root", "", "banking");

$message = ''; 

// Update row
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_id'], $_POST['data'], $_POST['table'])) {
    $id = intval($_POST['update_id']);
    $table = $_POST['table'];
    $data = $_POST['data'][$id];

    $setParts = [];
    foreach ($data as $column => $value) {
        if ($column === 'id') continue;
        $safeVal = mysqli_real_escape_string($conn, $value);
        $setParts[] = "`$column` = '$safeVal'";
    }
    $setQuery = implode(', ', $setParts);
    $updateQuery = "UPDATE `$table` SET $setQuery WHERE id = $id";

    if (mysqli_query($conn, $updateQuery)) {
        $message = "Updated"; 
    }
}

// Delete row
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'], $_POST['table'])) {
    $id = intval($_POST['delete_id']);
    $table = $_POST['table'];
    $deleteQuery = "DELETE FROM `$table` WHERE id = $id";

    if (mysqli_query($conn, $deleteQuery)) {
        $message = "Deleted";
    }
}

// Editable table (for users)
function showEditableTable($table, $conn) {
    $query = "SELECT * FROM $table";
    $result = mysqli_query($conn, $query);
    $fields = mysqli_fetch_fields($result);

    echo "<h3>Editable Table: $table</h3>";
    echo "<form method='POST'>";
    echo "<input type='hidden' name='table' value='$table'>";
    echo "<table><tr>";
    foreach ($fields as $field) {
        echo "<th>" . htmlspecialchars($field->name) . "</th>";
    }
    echo "<th>Actions</th></tr>";

    while ($row = mysqli_fetch_assoc($result)) {
        echo "<tr>";
        foreach ($row as $key => $value) {
            echo "<td><input type='text' name='data[{$row['id']}][$key]' value='" . htmlspecialchars($value) . "'></td>";
        }
        echo "<td>
                <button type='submit' name='update_id' value='{$row['id']}'>Update</button>
                <button type='submit' name='delete_id' value='{$row['id']}' onclick=\"return confirm('Delete this row?')\">Delete</button>
              </td>";
        echo "</tr>";
    }
    echo "</table>";
    echo "</form><br><br>";
}

// Read-only table
function showReadOnlyTable($table, $conn) {
    $query = "SELECT * FROM $table";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) == 0) {
        echo "<p>No data found in <b>$table</b> table.</p>";
        return;
    }

    echo "<h3>Table: $table</h3>";
    echo "<table><tr>";
    while ($field = mysqli_fetch_field($result)) {
        echo "<th>" . htmlspecialchars($field->name) . "</th>";
    }
    echo "</tr>";

    while ($row = mysqli_fetch_assoc($result)) {
        echo "<tr>";
        foreach ($row as $data) {
            echo "<td>" . htmlspecialchars($data) . "</td>";
        }
        echo "</tr>";
    }
    echo "</table><br><br>";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manager Dashboard</title>
    <style>
        html, body {
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
            padding: 30px;
        }

        h2 {
           font-size: 30px;
           color: #333;
        }

        form button {
            padding: 12px 25px;
            margin: 8px;
            background: rgb(38, 65, 132);
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 20px;
        }

        form button:hover {
            background: rgb(73, 132, 243);
        }

        table {
            margin: 20px auto;
            border-collapse: collapse;
            width: 80%;
            background: white;
        }

        th, td {
            border: 1px solid #888;
            padding: 10px 12px;
        }

        th {
            background: #eee;
        }

        input[type="text"] {
            width: 100%;
            border: none;
            padding: 6px;
            background: #f9f9f9;
        }

        a {
            display: inline-block;
            margin-top: 30px;
            text-decoration: none;
            color: maroon;
            font-size: 24px;
            font-weight: bold;
        }

        a:hover {
            text-decoration: underline;
        }

        .alert {
            position: fixed;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            background-color: #28a745;
            color: white;
            padding: 10px 28px;
            font-size: 14px;
            font-weight: bold;
            border-radius: 5px;
            box-shadow: 0px 0px 10px rgba(0,0,0,0.2);
            z-index: 9999;
            animation: fadeOut 3s forwards;
        }

        @keyframes fadeOut {
            0% { opacity: 1; }
            70% { opacity: 1; }
            100% { opacity: 0; display: none; }
        }
    </style>
</head>
<body>

<div class="background"></div>

<?php if (!empty($message)): ?>
    <div class="alert"><?= htmlspecialchars($message) ?></div>
<?php endif; ?>

<div class="content">
    <h2>Welcome Manager: <?= htmlspecialchars($_SESSION['manager_name']); ?></h2><br>

    <form method="POST">
        <button type="submit" name="table" value="users">Show Users</button>
        <button type="submit" name="table" value="transactions">Show Transactions</button>
        <button type="submit" name="table" value="bills">Show Bills</button><br><br>
    </form>

    <?php
    if (isset($_POST['table'])) {
        $table = $_POST['table'];
        if ($table === 'users') {
            showEditableTable($table, $conn);
        } elseif (in_array($table, ['transactions', 'bills'])) {
            showReadOnlyTable($table, $conn);
        } else {
            echo "<p>Invalid table selected.</p>";
        }
    }
    ?>

    <a href="logout_manager.php">Logout</a>
</div>

</body>
</html>
