<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Billing Management</title>
    <link rel="stylesheet" href="style.css"> <!-- Optional: External CSS File -->
    <script>
        function addProduct() {
            const productSelect = document.getElementById("product");
            const quantityInput = document.getElementById("quantity");
            const invoiceTable = document.getElementById("invoice-table").getElementsByTagName('tbody')[0];

            const productName = productSelect.options[productSelect.selectedIndex].text;
            const productPrice = parseFloat(productSelect.value);
            const quantity = parseInt(quantityInput.value);
            const productId = productSelect.options[productSelect.selectedIndex].getAttribute("data-id");

            // Check if a valid product is selected
            if (productSelect.value === "0") {
                alert("Please select a valid product.");
                return;
            }

            // Validate quantity input
            if (isNaN(quantity) || quantity <= 0) {
                alert("Please enter a valid quantity greater than 0.");
                quantityInput.value = ""; // Clear the input field
                return;
            }

            const totalPrice = (productPrice * quantity).toFixed(2);

            const row = invoiceTable.insertRow();
            row.innerHTML = `
                <td>${productName}</td>
                <td>${quantity}</td>
                <td>₹${productPrice.toFixed(2)}</td>
                <td>₹${totalPrice}</td>
                <td><button onclick="removeProduct(this)">Remove</button></td>
            `;

            // Append product and quantity to the form
            const productInput = document.createElement("input");
            productInput.type = "hidden";
            productInput.name = "products[]"; 
            productInput.value = productId;

            const quantityInputHidden = document.createElement("input");
            quantityInputHidden.type = "hidden";
            quantityInputHidden.name = "quantities[]"; 
            quantityInputHidden.value = quantity;

            const productForm = document.getElementById("billing-form");
            productForm.appendChild(productInput);
            productForm.appendChild(quantityInputHidden);

            updateTotal();
            quantityInput.value = ""; // Clear the quantity input
        }

        function removeProduct(button) {
            const row = button.parentElement.parentElement;
            row.parentElement.removeChild(row);
            updateTotal();
        }

        function updateTotal() {
            const invoiceTable = document.getElementById("invoice-table").getElementsByTagName('tbody')[0];
            const rows = invoiceTable.rows;
            let subtotal = 0;

            for (let i = 0; i < rows.length; i++) {
                subtotal += parseFloat(rows[i].cells[3].innerText.replace('₹', ''));
            }

            const gstCheckbox = document.getElementById("gst");
            const total = gstCheckbox.checked ? (subtotal * 1.18).toFixed(2) : subtotal.toFixed(2);
            document.getElementById("subtotal").innerText = `₹${subtotal.toFixed(2)}`;
            document.getElementById("total").innerText = `₹${total}`;
        }

        function togglePrintButton() {
            const paymentStatus = document.querySelector('input[name="payment-status"]:checked').value;
            const printButton = document.getElementById("print-btn");
            printButton.style.display = paymentStatus === "paid" ? "block" : "none";
        }

        function printInvoice() {
            const dealerName = document.getElementById("dealer").options[document.getElementById("dealer").selectedIndex].text;
            const dealerContact = document.getElementById("dealer-contact").innerText;
            const dealerCity = document.getElementById("dealer-city").innerText;

            const invoiceContent = `
                <div style="text-align: center;">
                    <img src="logo.png" alt="Company Logo" style="width: 100px; height: auto;">
                    <h2>Your Company Name</h2>
                    <h3>Invoice</h3>
                </div>
                <div>
                    <strong>Dealer Name:</strong> ${dealerName}<br>
                    <strong>Contact No:</strong> ${dealerContact}<br>
                    <strong>City:</strong> ${dealerCity}<br>
                </div>
                <table style="width: 100%; border-collapse: collapse; margin-top: 20px;">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Quantity</th>
                            <th>Price (₹)</th>
                            <th>Total (₹)</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${Array.from(document.getElementById("invoice-table").getElementsByTagName('tbody')[0].rows).map(row => `
                            <tr>
                                <td>${row.cells[0].innerText}</td>
                                <td>${row.cells[1].innerText}</td>
                                <td>${row.cells[2].innerText}</td>
                                <td>${row.cells[3].innerText}</td>
                            </tr>
                        `).join('')}
                    </tbody>
                </table>
                <div style="margin-top: 20px;">
                    <p><strong>Subtotal:</strong> ${document.getElementById("subtotal").innerText}</p>
                    <p><strong>GST (18%):</strong> ${document.getElementById("gst").checked ? "Included" : "Not Applicable"}</p>
                    <p><strong>Total:</strong> ${document.getElementById("total").innerText}</p>
                </div>
            `;

            const newWindow = window.open('', '', 'height=400,width=600');
            newWindow.document.write('<html><head><title>Invoice</title>');
            newWindow.document.write('<style>table {width: 100%; border-collapse: collapse;} th, td {border: 1px solid #000; padding: 8px; text-align: left;} h3 {text-align: center;} </style>');
            newWindow.document.write('</head><body>');
            newWindow.document.write(invoiceContent);
            newWindow.document.write('</body></html>');
            newWindow.document.close();
            newWindow.print();
        }

        function showPopup(message) {
            const popup = document.createElement('div');
            popup.id = 'success-popup';
            popup.style.position = 'fixed';
            popup.style.top = '20%';
            popup.style.left = '50%';
            popup.style.transform = 'translate(-50%, -50%)';
            popup.style.padding = '20px';
            popup.style.backgroundColor = '#4CAF50';
            popup.style.color = 'white';
            popup.style.borderRadius = '10px';
            popup.innerText = message;

            document.body.appendChild(popup);

            setTimeout(function() {
                popup.remove();
            }, 3000); // Remove popup after 3 seconds
        }

        window.onload = function() {
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.has('status') && urlParams.get('status') === 'success') {
                showPopup('Invoice submitted successfully!');
            }
        };
    </script>
</head>
<body>

    <?php include 'nav.php'; ?> <!-- Include Sidebar -->

    <div class="content">
        <h2>Billing Management</h2>

        <form id="billing-form" action="submit_invoice.php" method="POST">
            <div class="billing-section">
                <div class="form-group">
                    <label for="dealer">Select Dealer:</label>
                    <select id="dealer" name="dealer_id" required onchange="updateDealerDetails()">
                        <option value="">Select a dealer</option>
                        <?php
                        include 'connection.php'; // Include your database connection
                        $result = mysqli_query($conn, "SELECT id, dealer_name, contact_no, city_id FROM dealers");
                        while ($row = mysqli_fetch_assoc($result)) {
                            echo "<option value='{$row['id']}' data-contact='{$row['contact_no']}' data-city='{$row['city_id']}'>{$row['dealer_name']}</option>";
                        }
                        ?>
                    </select>
                </div>

                <div id="dealer-contact" style="display:none;"></div>
                <div id="dealer-city" style="display:none;"></div>

                <div class="form-group">
                    <label for="product">Select Product:</label>
                    <select id="product" name="product_id" required>
                        <option value="0">Select a product</option>
                        <?php
                        $result = mysqli_query($conn, "SELECT id, name, price FROM products");
                        while ($row = mysqli_fetch_assoc($result)) {
                            echo "<option value='{$row['price']}' data-id='{$row['id']}'>{$row['name']} - ₹{$row['price']}</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="quantity">Quantity:</label>
                    <input type="number" id="quantity" name="quantity" min="1" required>
                </div>

                <button type="button" onclick="addProduct()">Add More</button>
            </div>

            <div class="form-group">
                <input type="checkbox" id="gst" name="gst" onchange="updateTotal()">
                <label for="gst">Include GST (18%)</label>
            </div>

            <div class="form-group">
                <label>Payment Status:</label>
                <input type="radio" name="payment-status" value="pending" checked onclick="togglePrintButton()"> Pending
                <input type="radio" name="payment-status" value="paid" onclick="togglePrintButton()"> Paid
            </div>

            <div class="form-group">
                <h3>Invoice</h3>
                <table id="invoice-table">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Quantity</th>
                            <th>Price (₹)</th>
                            <th>Total (₹)</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Products will be added here -->
                    </tbody>
                </table>
                <p>Subtotal: ₹<span id="subtotal">0.00</span></p>
                <p>Total: ₹<span id="total">0.00</span></p>
            </div>

            <input type="submit" value="Submit Invoice">
            <button type="button" id="print-btn" style="display:none;" onclick="printInvoice()">Print Invoice</button>
        </form>
    </div>

    <script>
        function updateDealerDetails() {
            const dealerSelect = document.getElementById("dealer");
            const selectedOption = dealerSelect.options[dealerSelect.selectedIndex];

            document.getElementById("dealer-contact").innerText = "Contact No: " + selectedOption.getAttribute("data-contact");
            document.getElementById("dealer-city").innerText = "City: " + selectedOption.getAttribute("data-city");

            document.getElementById("dealer-contact").style.display = "block";
            document.getElementById("dealer-city").style.display = "block";
        }
    </script>

</body>
</html>
