# 📦 Inventory & Accounting Management System (Laravel 11)

A simplified **Inventory Management System with Double Entry Accounting** built using Laravel 11 and MySQL.

This project was developed as part of a Mid-Level Laravel Hiring Task (2026).

---

## 🌐 Live Demo

🔗 URL: https://zsinventory.smcglobal.shop/

**Admin Login Credentials**
**Email**: admin@gmail.com
**Password**: 123456


---

## 🏗️ Tech Stack

- **Framework:** Laravel 11
- **Database:** MySQL
- **Frontend:** Blade + Bootstrap
- **Architecture:** MVC + Service Layer (AccountingService)
- **Accounting Method:** Double Entry Bookkeeping

---

## 🚀 Core Features

### 📦 Inventory Management
- Product CRUD
- Opening stock & current stock tracking
- Automatic stock deduction on sale
- Stock restoration on sale deletion

### 🛒 Sales Module
- Multi-product sale entry
- Discount & VAT support
- Partial payment handling
- Automatic due calculation

### 📘 Double Entry Accounting
Every sale automatically generates journal entries:

- Debit:  Receivable
- Credit: Sales Revenue
- Credit: VAT Payable
- Debit: Cost of Goods Sold (COGS)
- Credit: Inventory

✔ Journal balancing enforced  
✔ Transaction-safe operations  
✔ Concurrency-safe stock update

---

## 📊 Reports

### 1️⃣ Date-wise Financial Report
- Total Sales
- Total VAT
- Total COGS
- Net Profit

### 2️⃣ Profit Report
- Revenue (Income Accounts)
- Expense
- Net Profit Calculation

### 3️⃣ Account Ledger
- Debit/Credit history
- Running balance
- Filter by date

---

## 🧾 Accounting Structure

### Chart of Accounts
- Cash
- Accounts Receivable
- Inventory
- Sales Revenue
- VAT Payable
- Cost of Goods Sold

### Journal Rule
SUM(Debit) = SUM(Credit)
Validation enforced at backend.

---

## 🧱 Database Structure
customers → sales → sale_items → products
sales → journal_entries → journal_entry_lines


---

## ⚙️ Local Setup Instructions

### Requirements
- PHP >= 8.2
- Composer
- MySQL
- Laravel 11

### Installation

git clone https://github.com/your-username/inventory-system.git
cd inventory-system

composer install
cp .env.example .env
php artisan key:generate

Configure .env
DB_DATABASE=inventory_db
DB_USERNAME=root
DB_PASSWORD=

Run Migration & Seed
php artisan migrate --seed

Start Server
php artisan serve

### Data Integrity & Safety

- DB Transactions used for sale creation & deletion

- lockForUpdate() prevents stock race condition

- Journal auto-reversed on sale delete

- Backend calculation (no frontend trust)

### Business Flow
Sale Create
↓
Stock Reduce
↓
Journal Entry Auto Create
↓
Reports Updated Automatically

### Project Highlights

- Clean Eloquent relationships

- Service-based accounting logic

- Date-filtered reporting

- Production-ready structure

- Error handling & edge-case protection

### Author

Developed by Sujon
Laravel Developer
Mid-Level Hiring Task – 2026
