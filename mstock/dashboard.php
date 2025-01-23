<?php
include 'db.php';

// Fetch data for graphs
$commodityData = $pdo->query("SELECT name, SUM(quantity) as total_stock FROM commodities c LEFT JOIN stock_in si ON c.id = si.commodity_id GROUP BY c.id")->fetchAll(PDO::FETCH_ASSOC);
$districtData = $pdo->query("SELECT d.name, SUM(so.quantity) as total_distributed FROM districts d LEFT JOIN stock_out so ON d.id = so.district_id GROUP BY d.id")->fetchAll(PDO::FETCH_ASSOC);

// Monthly consumption trends
$monthlyData = $pdo->query("SELECT MONTH(created_at) as month, SUM(quantity) as total FROM stock_out GROUP BY MONTH(created_at)")->fetchAll(PDO::FETCH_ASSOC);
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

    <h1>Dashboard</h1>

    <div class="charts">
        <div>
            <h2>Commodity Stock Summary</h2>
            <canvas id="commodityChart"></canvas>
        </div>
        <div>
            <h2>District Distribution Summary</h2>
            <canvas id="districtChart"></canvas>
        </div>
        <div>
            <h2>Monthly Consumption Trend</h2>
            <canvas id="monthlyChart"></canvas>
        </div>
    </div>

    <script>
        const commodityData = <?= json_encode($commodityData); ?>;
        const districtData = <?= json_encode($districtData); ?>;
        const monthlyData = <?= json_encode($monthlyData); ?>;

        // Commodity chart
        new Chart(document.getElementById('commodityChart'), {
            type: 'bar',
            data: {
                labels: commodityData.map(c => c.name),
                datasets: [{
                    label: 'Total Stock',
                    data: commodityData.map(c => c.total_stock || 0),
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                }]
            }
        });

        // District chart
        new Chart(document.getElementById('districtChart'), {
            type: 'bar',
            data: {
                labels: districtData.map(d => d.name),
                datasets: [{
                    label: 'Total Distributed',
                    data: districtData.map(d => d.total_distributed || 0),
                    backgroundColor: 'rgba(153, 102, 255, 0.2)',
                    borderColor: 'rgba(153, 102, 255, 1)',
                    borderWidth: 1
                }]
            }
        });

        // Monthly chart
        new Chart(document.getElementById('monthlyChart'), {
            type: 'line',
            data: {
                labels: monthlyData.map(m => `Month ${m.month}`),
                datasets: [{
                    label: 'Consumption',
                    data: monthlyData.map(m => m.total || 0),
                    backgroundColor: 'rgba(255, 159, 64, 0.2)',
                    borderColor: 'rgba(255, 159, 64, 1)',
                    borderWidth: 1
                }]
            }
        });
    </script>
</body>
</html>
