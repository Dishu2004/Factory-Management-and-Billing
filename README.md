# 🏭 Factory Billing System

A robust and user-friendly **billing and factory management system** designed to streamline invoice generation, manage pending/paid bills, and visualize business growth with interactive charts. Built using **PHP, MySQL, HTML, CSS, and JavaScript**, this system is ideal for manufacturing units and MSMEs.

---

## 🚀 Features

### 🧾 Billing & Invoicing
- Create and manage bills for customers
- Mark bills as **Pending** or **Paid**
- Generate printable invoice receipts

### 💰 Pending Bills Management
- Filter and track all unpaid bills
- View customer-wise pending amount
- Update bill status instantly

### 📊 Visual Growth Analytics
- Dashboard with **monthly/yearly growth charts**
- Pie charts for expenses breakdown (materials, labor, etc.)
- Line/Bar charts for sales over time
- Compare growth against previous years

### 🧑‍💼 Admin Panel
- Manage customers and dealers
- Add/edit/delete products and raw materials
- View detailed transaction and invoice history

### 📂 Data Management
- MySQL relational database
- Organized schema for invoices, customers, expenses, and product batches

---

## 🧱 Tech Stack

| Layer      | Technology               |
|------------|--------------------------|
| Frontend   | HTML5, CSS3, JavaScript  |
| Backend    | PHP (Core PHP)           |
| Database   | MySQL                    |
| Visualization | Chart.js or Google Charts (JavaScript libraries) |

---

## 🗃️ Database Structure (MySQL)

```sql
users(user_id, name, email, password)
customers(customer_id, name, contact, address)
invoices(invoice_id, customer_id, amount, status, created_at)
products(product_id, name, price)
invoice_items(item_id, invoice_id, product_id, quantity, total_price)
expenses(expense_id, type, amount, month, year)

