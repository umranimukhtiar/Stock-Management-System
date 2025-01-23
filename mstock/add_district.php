<?php
include 'db.php';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $province = $_POST['province'];

    $stmt = $pdo->prepare("INSERT INTO districts (name, province, created_at) VALUES (?, ?, NOW())");
    $stmt->execute([$name, $province]);
    $message = "District added successfully!";
}

// Fetch all districts for display
$districts = $pdo->query("SELECT * FROM districts")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add District</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <nav class="navbar">
        <a href="dashboard.php">Dashboard</a>
        <a href="stock_in.php">Stock In</a>
        <a href="stock_out.php">Stock Out</a>
        <a href="report.php">Reports</a>
        <a href="add_commodity.php">Add Commodity</a>
        <a href="add_district.php">Add District</a>
    </nav>

    <h1>Add District</h1>

    <?php if (!empty($message)): ?>
        <p class="success"><?= $message; ?></p>
    <?php endif; ?>

    <form method="POST">
        <label>Name:</label>
        <input type="text" name="name" required><br>

        <label>Province:</label>
        <input type="text" name="province" required><br>

        <button type="submit">Add District</button>
    </form>

    <h2>Existing Districts</h2>
    <table>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Province</th>
            <th>Created At</th>
        </tr>
        <?php foreach ($districts as $district): ?>
            <tr>
                <td><?= $district['id']; ?></td>
                <td><?= $district['name']; ?></td>
                <td><?= $district['province']; ?></td>
                <td><?= $district['created_at']; ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
