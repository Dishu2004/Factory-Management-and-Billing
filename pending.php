<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pending Bills</title>
    <link rel="stylesheet" href="style.css"> <!-- Link to your external CSS -->
</head>
<body>

    <?php include 'nav.php'; ?> <!-- Include Sidebar -->
    <?php include 'connection.php'; ?> <!-- Include your database connection -->

    <div class="content">
        <h1>Pending Bills</h1>

        <div class="total-container">
            <?php
            // Fetch total pending bills
            $total_pending_query = "SELECT SUM(total) AS total_pending FROM invoices WHERE payment_status = 'pending'";
            $total_pending_result = mysqli_query($conn, $total_pending_query);
            $total_pending_row = mysqli_fetch_assoc($total_pending_result);
            $total_pending = $total_pending_row['total_pending'] ? $total_pending_row['total_pending'] : 0;
            ?>
            <h2>Total Pending Bills: <span id="total-pending-bills">₹<?php echo number_format($total_pending, 2); ?></span></h2> <!-- Total in INR -->
        </div>

        <div class="search-container">
            <input type="text" id="search" placeholder="Search by Dealer Name..." onkeyup="filterBills()">
        </div>

        <div class="table-container">
            <table class="table" id="pending-bills-table">
                <thead>
                    <tr>
                        <th>Dealer Name</th>
                        <th>Date of Bill</th>
                        <th>Amount Pending</th> <!-- New Column for Amount -->
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Fetch pending bills
                    $query = "SELECT d.dealer_name, i.created_at, i.total, i.id AS invoice_id FROM invoices i INNER JOIN dealers d ON i.dealer_id = d.id WHERE i.payment_status = 'pending'";
                    $result = mysqli_query($conn, $query);

                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "<tr data-invoice-id='{$row['invoice_id']}'>
                                <td>{$row['dealer_name']}</td>
                                <td>{$row['created_at']}</td>
                                <td>₹" . number_format($row['total'], 2) . "</td>
                                <td>
                                    <button class='pay-btn' onclick='switchToPaid(this, \"{$row['dealer_name']}\", {$row['invoice_id']})'>Switch to Paid</button>
                                    <button class='print-btn' style='display:none;' onclick='printInvoice(this, \"{$row['dealer_name']}\", {$row['invoice_id']})'>Print Invoice</button>
                                </td>
                              </tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        function filterBills() {
            const input = document.getElementById('search');
            const filter = input.value.toLowerCase();
            const table = document.getElementById('pending-bills-table');
            const tr = table.getElementsByTagName('tr');

            for (let i = 1; i < tr.length; i++) {
                const td = tr[i].getElementsByTagName('td')[0];
                if (td) {
                    const txtValue = td.textContent || td.innerText;
                    tr[i].style.display = txtValue.toLowerCase().indexOf(filter) > -1 ? "" : "none";
                }       
            }
        }

        function switchToPaid(button, dealerName, invoiceId) {
            const row = button.parentElement.parentElement;

            // Update total pending amount
            const amountPending = parseFloat(row.cells[2].innerText.replace('₹', '').replace(',', ''));
            const totalPendingBillsElement = document.getElementById('total-pending-bills');
            const currentTotal = parseFloat(totalPendingBillsElement.innerText.replace('₹', '').replace(',', ''));
            const updatedTotal = currentTotal - amountPending;
            totalPendingBillsElement.innerText = `₹${updatedTotal.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",")}`;

            // Show the print button and hide the pay button
            row.querySelector('.pay-btn').style.display = 'none';
            const printButton = row.querySelector('.print-btn');
            printButton.style.display = 'inline-block';

            // Update the invoice status in the database (pseudo code for AJAX)
            // Uncomment below and use AJAX to update your database
            // fetch(`update_invoice.php?id=${invoiceId}`, { method: 'POST' });

            alert(`${dealerName} has been marked as paid!`);
        }

        function printInvoice(button, dealerName, invoiceId) {
            const row = button.parentElement.parentElement;
            const amountPending = row.cells[2].innerText; // Get the amount pending
            const date = row.cells[1].innerText; // Get the date of bill

            // Create a print-friendly invoice
            const invoiceContent = `
                <div style="text-align: center;">
                    <h2>Your Company Name</h2>
                    <h3>Invoice</h3>
                    <p><strong>Dealer Name:</strong> ${dealerName}</p>
                    <p><strong>Date of Bill:</strong> ${date}</p>
                    <p><strong>Amount Pending:</strong> ${amountPending}</p>
                </div>
            `;

            const newWindow = window.open('', '', 'height=400,width=600');
            newWindow.document.write('<html><head><title>Invoice</title></head><body>');
            newWindow.document.write(invoiceContent);
            newWindow.document.write('</body></html>');
            newWindow.document.close();
            newWindow.print();

            // Remove the row from the table after printing
            row.remove();
        }
    </script>

</body>
</html>
