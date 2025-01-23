<?php
include 'db.php';

$message = ""; // Variable to store messages for the user

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $folio_no = $_POST['folio_no'];
    $commodity_id = $_POST['commodity_id'];
    $quantity = $_POST['quantity'];
    $remarks = $_POST['remarks'];

    // Insert the new stock entry
    $stmt = $pdo->prepare("INSERT INTO stock_in (folio_no, commodity_id, quantity, remarks, created_at) VALUES (?, ?, ?, ?, NOW())");
    $stmt->execute([$folio_no, $commodity_id, $quantity, $remarks]);

    $message = "Success: Stock for folio #$folio_no has been added successfully.";
}

// Fetch commodities and all stock entries for display
$commodities = $pdo->query("SELECT * FROM commodities")->fetchAll(PDO::FETCH_ASSOC);
$stock_history = $pdo->query("
    SELECT si.folio_no, si.commodity_id, c.name AS commodity_name, si.quantity, si.remarks, si.created_at 
    FROM stock_in si 
    JOIN commodities c ON si.commodity_id = c.id 
    ORDER BY si.created_at DESC
")->fetchAll(PDO::FETCH_ASSOC);

// Calculate total stock for each commodity
$total_stock = $pdo->query("
    SELECT c.id, c.name AS commodity_name, SUM(si.quantity) AS total_quantity 
    FROM stock_in si 
    JOIN commodities c ON si.commodity_id = c.id 
    GROUP BY c.id
")->fetchAll(PDO::FETCH_ASSOC);
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

    <h1>Stock In</h1>

    <?php if (!empty($message)): ?>
        <p style="color: green;"><?= $message; ?></p>
    <?php endif; ?>

    <form method="POST">
        <label>Folio No:</label>
        <input type="text" name="folio_no" required><br>

        <label>Commodity:</label>
        <select name="commodity_id" required>
            <?php foreach ($commodities as $commodity): ?>
                <option value="<?= $commodity['id']; ?>"><?= $commodity['name']; ?></option>
            <?php endforeach; ?>
        </select><br>

        <label>Quantity:</label>
        <input type="number" name="quantity" required><br>

        <label>Remarks:</label>
        <textarea name="remarks"></textarea><br>

        <button type="submit">Add Stock</button>
    </form>

    <h2>Stock Summary</h2>
    <table border="1">
        <tr>
            <th>Commodity</th>
            <th>Total Quantity</th>
        </tr>
        <?php foreach ($total_stock as $stock): ?>
            <tr>
                <td><?= $stock['commodity_name']; ?></td>
                <td><?= $stock['total_quantity']; ?></td>
            </tr>
        <?php endforeach; ?>
    </table>

    <h2>Stock In History</h2>
    <table border="1">
        <tr>
            <th>Folio No</th>
            <th>Commodity</th>
            <th>Quantity</th>
            <th>Remarks</th>
            <th>Date</th>
        </tr>
        <?php foreach ($stock_history as $entry): ?>
            <tr>
                <td><?= $entry['folio_no']; ?></td>
                <td><?= $entry['commodity_name']; ?></td>
                <td><?= $entry['quantity']; ?></td>
                <td><?= $entry['remarks']; ?></td>
                <td><?= $entry['created_at']; ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
