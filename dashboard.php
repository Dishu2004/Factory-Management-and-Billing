<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <style>
        /* Internal CSS for styling */
        body {
            background-color: #f4f4f4;
            font-family: Arial, sans-serif;
        }

        .dashboard-content {
            margin-left: 260px; /* Adjust based on your sidebar width */
            padding: 20px;
        }

        .box-container {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-top: 20px;
        }

        .box {
            background-color: #fff;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            text-align: center;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        /* Make the numbers larger and bolder */
        .box h2 {
            font-size: 2.5em; /* Bigger font size for numbers */
            color: #333; /* Darker color for better visibility */
            margin: 10px 0; /* Spacing around the number */
        }

        .box p {
            font-size: 1.2em; /* Slightly larger font for the labels */
            color: #666; /* Lighter color for the labels */
        }
    </style>
</head>
<body>
    <!-- Including the Sidebar Navigation -->
    <?php include 'nav.php'; ?>

    <div class="dashboard-content">
        <h1>Dashboard</h1>

        <div class="box-container">
            <?php 
            // Include database connection
            include 'connection.php'; 

            // Fetch total cities
            $totalCitiesQuery = "SELECT COUNT(*) as count FROM cities";
            $totalCitiesResult = $conn->query($totalCitiesQuery);
            $totalCities = $totalCitiesResult->fetch_assoc()['count'];

            // Fetch total dealers
            $totalDealersQuery = "SELECT COUNT(*) as count FROM dealers";
            $totalDealersResult = $conn->query($totalDealersQuery);
            $totalDealers = $totalDealersResult->fetch_assoc()['count'];

            // Fetch total products
            $totalProductsQuery = "SELECT COUNT(*) as count FROM products";
            $totalProductsResult = $conn->query($totalProductsQuery);
            $totalProducts = $totalProductsResult->fetch_assoc()['count'];

            // Fetch total bills
            $totalBillsQuery = "SELECT COUNT(*) as count FROM invoices";
            $totalBillsResult = $conn->query($totalBillsQuery);
            $totalBills = $totalBillsResult->fetch_assoc()['count'];

            // Fetch pending amount
            $pendingAmountQuery = "SELECT SUM(total) as pending FROM invoices WHERE payment_status = 'pending'";
            $pendingAmountResult = $conn->query($pendingAmountQuery);
            $pendingAmount = $pendingAmountResult->fetch_assoc()['pending'] ?? 0;

            // Fetch total sales
            $totalSalesQuery = "SELECT SUM(total) as sales FROM invoices";
            $totalSalesResult = $conn->query($totalSalesQuery);
            $totalSales = $totalSalesResult->fetch_assoc()['sales'] ?? 0;

            // Fetch total spent on raw materials
            $totalRawMaterialsQuery = "SELECT SUM(price * quantity) as totalRaw FROM raw_materials";
            $totalRawMaterialsResult = $conn->query($totalRawMaterialsQuery);
            $totalRawMaterials = $totalRawMaterialsResult->fetch_assoc()['totalRaw'] ?? 0;

            // Fetch total spent on labor
            $totalLaborQuery = "SELECT SUM(amount) as totalLabor FROM labor_records";
            $totalLaborResult = $conn->query($totalLaborQuery);
            $totalLabor = $totalLaborResult->fetch_assoc()['totalLabor'] ?? 0;

            // Calculate total profit
            $totalProfit = $totalSales - ($totalRawMaterials + $totalLabor);
            ?>

            <div class="box">Total No. of Cities: <h2><?php echo $totalCities; ?></h2></div>
            <div class="box">Total No. of Dealers: <h2><?php echo $totalDealers; ?></h2></div>
            <div class="box">Total No. of Products: <h2><?php echo $totalProducts; ?></h2></div>
            <div class="box">Total No. of Bills Till Now: <h2><?php echo $totalBills; ?></h2></div>
            <div class="box">Amount of Pendings: <h2>₹<?php echo number_format($pendingAmount, 2); ?></h2></div>
            <div class="box">Total Sales Till Now: <h2>₹<?php echo number_format($totalSales, 2); ?></h2></div>
            <div class="box">Total Spent on Raw Material: <h2>₹<?php echo number_format($totalRawMaterials, 2); ?></h2></div>
            <div class="box">Total Spent on Labor: <h2>₹<?php echo number_format($totalLabor, 2); ?></h2></div>
            <div class="box">Total Profit: <h2>₹<?php echo number_format($totalProfit, 2); ?></h2></div>
        </div>
    </div>
</body>
</html>
