<?php
include 'db.php';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $description = $_POST['description'];

    $stmt = $pdo->prepare("INSERT INTO commodities (name, description, created_at) VALUES (?, ?, NOW())");
    $stmt->execute([$name, $description]);
    $message = "Commodity added successfully!";
}

// Fetch all commodities for display
$commodities = $pdo->query("SELECT * FROM commodities")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Commodity</title>
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

    <h1>Add Commodity</h1>

    <?php if (!empty($message)): ?>
        <p class="success"><?= $message; ?></p>
    <?php endif; ?>

    <form method="POST">
        <label>Name:</label>
        <input type="text" name="name" required><br>

        <label>Description:</label>
        <textarea name="description"></textarea><br>

        <button type="submit">Add Commodity</button>
    </form>

    <h2>Existing Commodities</h2>
    <table>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Description</th>
            <th>Created At</th>
        </tr>
        <?php foreach ($commodities as $commodity): ?>
            <tr>
                <td><?= $commodity['id']; ?></td>
                <td><?= $commodity['name']; ?></td>
                <td><?= $commodity['description']; ?></td>
                <td><?= $commodity['created_at']; ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
