<?php
include 'db.php';

$message = ""; // Variable to store messages for the user

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $commodity_id = $_POST['commodity_id'];
    $district_id = $_POST['district_id'];
    $quantity = $_POST['quantity'];

    // Check the available stock for the selected commodity
    $stmt = $pdo->prepare("SELECT SUM(quantity) AS total_quantity FROM stock_in WHERE commodity_id = ?");
    $stmt->execute([$commodity_id]);
    $stock = $stmt->fetch(PDO::FETCH_ASSOC);
    $available_quantity = $stock['total_quantity'] ?? 0;

    if ($available_quantity < $quantity || $available_quantity <= 0) {
        $message = "Error: Insufficient stock for the selected commodity. Please stock in first.";
    } else {
        // Proceed with stock out
        $pdo->beginTransaction(); // Start transaction
        try {
            $stmt = $pdo->prepare("INSERT INTO stock_out (commodity_id, district_id, quantity, created_at) VALUES (?, ?, ?, NOW())");
            $stmt->execute([$commodity_id, $district_id, $quantity]);

            $stmt = $pdo->prepare("UPDATE stock_in SET quantity = quantity - ? WHERE commodity_id = ?");
            $stmt->execute([$quantity, $commodity_id]);

            $pdo->commit(); // Commit transaction
            $message = "Success: Stock out was performed successfully.";
        } catch (Exception $e) {
            $pdo->rollBack(); // Rollback transaction in case of error
            $message = "Error: Unable to perform stock out. Please try again.";
        }
    }
}

// Fetch commodities and districts for the dropdown
$commodities = $pdo->query("SELECT * FROM commodities")->fetchAll(PDO::FETCH_ASSOC);
$districts = $pdo->query("SELECT * FROM districts")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <link rel="stylesheet" href="styles.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
    <h1>Stock Out</h1>

    <?php if (!empty($message)): ?>
        <p style="color: <?= strpos($message, 'Error') !== false ? 'red' : 'green'; ?>;">
            <?= $message; ?>
        </p>
    <?php endif; ?>

    <form method="POST">
        <label>Commodity:</label>
        <select name="commodity_id" required>
            <?php foreach ($commodities as $commodity): ?>
                <option value="<?= $commodity['id']; ?>"><?= $commodity['name']; ?></option>
            <?php endforeach; ?>
        </select><br>

        <label>District:</label>
        <select name="district_id" required>
            <?php foreach ($districts as $district): ?>
                <option value="<?= $district['id']; ?>"><?= $district['name']; ?></option>
            <?php endforeach; ?>
        </select><br>

        <label>Quantity:</label>
        <input type="number" name="quantity" required><br>

        <button type="submit">Stock Out</button>
    </form>
</body>
</html>
