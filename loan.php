<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yearly Sales Dashboard</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> <!-- Include Chart.js for graphing -->
    <style>
        /* General Styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        body {
            background-color: #000; /* Black background */
            color: white;
            display: flex;
            height: 100vh; /* Full height */
            flex-direction: column; /* Stack elements vertically */
        }

        /* Back Button Styles */
        .back-button {
            background-color: #1c1c1c; /* Dark background for button */
            color: #ff6600; /* Orange text */
            padding: 10px 20px; /* Padding around button */
            border: none; /* No border */
            border-radius: 5px; /* Rounded corners */
            cursor: pointer; /* Pointer cursor on hover */
            margin: 20px auto; /* Center button horizontally */
            transition: background-color 0.3s; /* Smooth transition */
        }

        .back-button:hover {
            background-color: #ff6600; /* Change background on hover */
            color: white; /* Change text color on hover */
        }

        /* Chart Container */
        .chart-container {
            width: 100%; /* Full width */
            height: 80vh; /* Use 80% of viewport height */
            max-width: 1200px; /* Max width of the chart */
            margin: 0 auto; /* Center horizontally */
            position: relative; /* For positioning the canvas */
        }

        h2 {
            text-align: center;
            margin-bottom: 20px; /* Space between title and chart */
        }

        canvas {
            width: 100% !important; /* Fill the container */
            height: 100% !important; /* Fill the container */
        }
    </style>
</head>
<body>

    <button class="back-button" onclick="window.location.href='dashboard.php';">Back to Dashboard</button>

    <?php 
    include 'connection.php'; // Your database connection

    // Fetch yearly growth data
    $yearlyGrowthData = [];
    $yearlyQuery = "SELECT YEAR(created_at) as year, SUM(total) as total_growth FROM invoices GROUP BY year";
    $yearlyResult = $conn->query($yearlyQuery);
    while ($row = $yearlyResult->fetch_assoc()) {
        $yearlyGrowthData[$row['year']] = $row['total_growth'];
    }

    // Prepare data for the chart
    $years = array_keys($yearlyGrowthData);
    $yearlyData = array_values($yearlyGrowthData);
    ?>

    <div class="chart-container">
        <h2>Yearly Sales</h2>
        <canvas id="yearly-sales-chart"></canvas>
    </div>

    <script>
        // Yearly Sales Line Chart
        const ctxYearly = document.getElementById('yearly-sales-chart').getContext('2d');
        const yearlySalesChart = new Chart(ctxYearly, {
            type: 'line',
            data: {
                labels: <?php echo json_encode($years); ?>, // Year labels
                datasets: [{
                    label: 'Yearly Sales',
                    data: <?php echo json_encode($yearlyData); ?>, // Yearly sales data
                    borderColor: '#36A2EB',
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    fill: true,
                    tension: 0.1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Sales Amount ($)'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Year'
                        }
                    }
                }
            }
        });
    </script>
</body>
</html>
