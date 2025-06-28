<?php
session_start();
if (!isset($_SESSION['admin_email'])) {
    header("Location: login_admin.php");
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

// Add new row
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_row'], $_POST['new'], $_POST['table'])) {
    $table = $_POST['table'];
    $new = $_POST['new'];

    $columns = [];
    $values = [];
    foreach ($new as $col => $val) {
        if ($col === 'id' || $val === '') continue;
        $columns[] = "`$col`";
        $values[] = "'" . mysqli_real_escape_string($conn, $val) . "'";
    }

    if (!empty($columns)) {
        $insertQuery = "INSERT INTO `$table` (" . implode(', ', $columns) . ") VALUES (" . implode(', ', $values) . ")";
        if (mysqli_query($conn, $insertQuery)) {
            $message = "Added";
        }
    }
}

// Show editable table
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

    // Add row form
    if ($table !== 'users') {
        echo "<tr>";
        foreach ($fields as $field) {
            $name = $field->name;
            if ($name === 'id') {
                echo "<td><em>Auto</em></td>";
            } else {
                echo "<td><input type='text' name='new[$name]'></td>";
            }
        }
        echo "<td><button type='submit' name='add_row' value='1'>Add</button></td>";
        echo "</tr>";
    }

    echo "</table>";
    echo "</form><br>";
}

// Show read-only table
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
    echo "</table><br>";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <style>
        * { box-sizing: border-box; }
        body, html {
            margin: 0; padding: 0;
            height: 100%;
            font-family: Arial, sans-serif;
        }

        .background-container {
            position: fixed;
            top: 0; left: 0;
            width: 100%;
            height: 100%;
            background: url('bg.png') no-repeat center center;
            background-size: cover;
            opacity: 0.5;
            z-index: -1;
        }

        .content {
            position: relative;
            z-index: 1;
            text-align: center;
            padding: 30px;
            background: rgba(0,0,0,0.6);
            color: white;
            min-height: 100vh;
        }

        h2 { 
            font-size: 30px;
            color: #f0f0f0; 
        }

        form button {
            padding: 10px 20px;
            margin: 5px;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 20px;
        }
        form button:hover { background: #0056b3; }

        table {
            margin: 20px auto;
            border-collapse: collapse;
            width: 90%;
            background: white;
            color: black;
        }
        th, td {
            border: 1px solid #888;
            padding: 8px 10px;
        }
        th { background: #eee; }

        input[type="text"] {
            width: 100%;
            border: none;
            padding: 4px;
            background: #f9f9f9;
        }
        a {
            display: inline-block;
            margin-top: 20px;
            text-decoration: none;
            color: maroon;
            font-weight: bold;
            font-size: 22px;
        }

        .alert-box {
            position: fixed;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            background: #28a745;
            color: white;
            padding: 10px 30px;
            font-size: 16px;
            font-weight: bold;
            border-radius: 6px;
            z-index: 9999;
            box-shadow: 0 0 10px rgba(0,0,0,0.3);
            animation: fadeOut 3s ease forwards;
        }

        @keyframes fadeOut {
            0% { opacity: 1; }
            70% { opacity: 1; }
            100% { opacity: 0; display: none; }
        }
    </style>
</head>
<body>

<div class="background-container"></div>

<?php if (!empty($message)): ?>
    <div class="alert-box"><?= htmlspecialchars($message); ?></div>
<?php endif; ?>

<div class="content">
    <h2>Welcome <?= htmlspecialchars($_SESSION['admin_name']); ?></h2><br><br>

    <form method="POST">
        <button type="submit" name="table" value="admins">Show Admins</button>
        <button type="submit" name="table" value="managers">Show Managers</button>
        <button type="submit" name="table" value="users">Show Users</button>
        <button type="submit" name="table" value="transactions">Show Transactions</button>
        <button type="submit" name="table" value="bills">Show Bills</button><br><br>
    </form>

    <?php
    if (isset($_POST['table'])) {
        $table = $_POST['table'];
        $editable_tables = ['admins', 'managers', 'users'];
        $readonly_tables = ['transactions', 'bills'];

        if (in_array($table, $editable_tables)) {
            showEditableTable($table, $conn);
        } elseif (in_array($table, $readonly_tables)) {
            showReadOnlyTable($table, $conn);
        } else {
            echo "<p>Invalid table selected.</p>";
        }
    }
    ?>

    <a href="logout_admin.php">Logout</a>
</div>

</body>
</html>
