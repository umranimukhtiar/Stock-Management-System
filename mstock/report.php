<?php
include 'db.php';

$filterDistrict = $_GET['district_id'] ?? null;
$filterCommodity = $_GET['commodity_id'] ?? null;
$fromDate = $_GET['from_date'] ?? null;
$toDate = $_GET['to_date'] ?? null;
$searchQuery = $_GET['search_query'] ?? null;
$page = $_GET['page'] ?? 1;
$recordsPerPage = 10;

// Calculate offset for pagination
$offset = ($page - 1) * $recordsPerPage;

// Base query
$query = "SELECT c.name as commodity, d.name as district, so.quantity, so.created_at 
          FROM stock_out so 
          JOIN commodities c ON so.commodity_id = c.id 
          JOIN districts d ON so.district_id = d.id 
          WHERE 1=1";

// Add filters
if ($filterDistrict) $query .= " AND so.district_id = $filterDistrict";
if ($filterCommodity) $query .= " AND so.commodity_id = $filterCommodity";
if ($fromDate && $toDate) $query .= " AND so.created_at BETWEEN '$fromDate' AND '$toDate'";
if ($searchQuery) {
    $query .= " AND (c.name LIKE '%$searchQuery%' 
                OR d.name LIKE '%$searchQuery%' 
                OR so.quantity LIKE '%$searchQuery%' 
                OR so.created_at LIKE '%$searchQuery%')";
}

// Count total records for pagination
$totalRecords = $pdo->query(str_replace("SELECT c.name as commodity, d.name as district, so.quantity, so.created_at", "SELECT COUNT(*)", $query))->fetchColumn();
$totalPages = ceil($totalRecords / $recordsPerPage);

// Add limit and offset for pagination
$query .= " LIMIT $recordsPerPage OFFSET $offset";
$reports = $pdo->query($query)->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Report</title>
    <link rel="stylesheet" href="styles.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.25/jspdf.plugin.autotable.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
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

<div class="container">
    <h1>Report</h1>
    <form method="GET" class="filter-form">
        <select name="district_id">
            <option value="">All Districts</option>
            <?php foreach ($pdo->query("SELECT * FROM districts")->fetchAll(PDO::FETCH_ASSOC) as $district): ?>
                <option value="<?= $district['id']; ?>" <?= $filterDistrict == $district['id'] ? 'selected' : ''; ?>>
                    <?= $district['name']; ?>
                </option>
            <?php endforeach; ?>
        </select>

        <select name="commodity_id">
            <option value="">All Commodities</option>
            <?php foreach ($pdo->query("SELECT * FROM commodities")->fetchAll(PDO::FETCH_ASSOC) as $commodity): ?>
                <option value="<?= $commodity['id']; ?>" <?= $filterCommodity == $commodity['id'] ? 'selected' : ''; ?>>
                    <?= $commodity['name']; ?>
                </option>
            <?php endforeach; ?>
        </select>

        <input type="date" name="from_date" value="<?= $fromDate; ?>" placeholder="From Date">
        <input type="date" name="to_date" value="<?= $toDate; ?>" placeholder="To Date">
        <input type="text" name="search_query" value="<?= $searchQuery; ?>" placeholder="Search">
        <button type="submit">Filter</button>
    </form>

    <div class="export-buttons">
        <button onclick="exportToCSV()">Export CSV</button>
        <button onclick="exportToExcel()">Export Excel</button>
        <button onclick="exportToPDF()">Export PDF</button>
    </div>

    <input type="text" id="tableSearch" placeholder="Search table..." onkeyup="searchTable()">

    <table id="reportTable">
        <thead>
            <tr>
                <th>Commodity</th>
                <th>District</th>
                <th>Quantity</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($reports as $report): ?>
                <tr>
                    <td><?= $report['commodity']; ?></td>
                    <td><?= $report['district']; ?></td>
                    <td><?= $report['quantity']; ?></td>
                    <td><?= $report['created_at']; ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <ul class="pagination">
        <?php if ($page > 1): ?>
            <a href="?<?= http_build_query(array_merge($_GET, ['page' => $page - 1])); ?>">&laquo; Prev</a>
        <?php endif; ?>
        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <a href="?<?= http_build_query(array_merge($_GET, ['page' => $i])); ?>" class="<?= $i == $page ? 'active' : ''; ?>">
                <?= $i; ?>
            </a>
        <?php endfor; ?>
        <?php if ($page < $totalPages): ?>
            <a href="?<?= http_build_query(array_merge($_GET, ['page' => $page + 1])); ?>">Next &raquo;</a>
        <?php endif; ?>
    </ul>
</div>

<script>
    function exportToCSV() {
        const rows = [...document.querySelectorAll('#reportTable tr')].map(row => 
            [...row.querySelectorAll('th, td')].map(cell => cell.innerText).join(',')
        ).join('\n');

        const blob = new Blob([rows], { type: 'text/csv;charset=utf-8;' });
        const url = URL.createObjectURL(blob);
        const link = document.createElement('a');
        link.href = url;
        link.download = 'report.csv';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }

    function exportToExcel() {
        const workbook = XLSX.utils.table_to_book(document.querySelector('#reportTable'), { sheet: "Report" });
        XLSX.writeFile(workbook, 'report.xlsx');
    }

    function exportToPDF() {
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF();
        doc.autoTable({ html: '#reportTable' });
        doc.save('report.pdf');
    }

    function searchTable() {
        const query = document.getElementById('tableSearch').value.toLowerCase();
        const rows = document.querySelectorAll('#reportTable tbody tr');
        rows.forEach(row => {
            const text = row.innerText.toLowerCase();
            row.style.display = text.includes(query) ? '' : 'none';
        });
    }
</script>

</body>
</html>
