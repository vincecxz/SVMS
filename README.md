# Student Affairs Office (SAO) - Violation Monitoring System

<div align="center">
    <img src="assets/img/ctu_logo.png" alt="CTU Logo" width="150">
</div>

A comprehensive web-based system designed for managing student violations and disciplinary records at the Student Affairs Office. This system streamlines the process of tracking student infractions, generating reports, and maintaining accurate disciplinary records.

## 🌟 Key Features

- **Student Management**
  - Add, edit, and manage student records
  - Import students via Excel/CSV
  - View student profiles and violation history

- **Program & Section Management**
  - Manage academic programs
  - Configure program sections
  - Easy program-section mapping

- **Violation Reporting**
  - Record student violations
  - Track disciplinary actions
  - Monitor violation status

- **Document Generation**
  - Generate Good Moral Certificates
  - Export reports to Excel/PDF
  - Customizable document templates

- **User Management**
  - Role-based access control (Admin/Staff)
  - Secure authentication system
  - Password management

## 🔧 Technical Requirements

- **PHP** >= 8.0
- **MySQL/MariaDB** >= 5.7
- **Composer** (Latest version)
- **Web Server**: Apache/Nginx
- **Browser**: Chrome, Firefox, Safari, Edge (Latest versions)

## 📦 Installation Guide



### 1. Install Dependencies
```bash
composer install
```

### 2. Database Configuration
1. Create a new MySQL database
2. Import the database schema:
   ```bash
   mysql -u your_username -p your_database_name < database/sao_db.sql
   ```
3. Configure database connection:
   - Copy `config/database.example.php` to `config/database.php`
   - Update the database credentials in `config/database.php`

## 🔐 Default Access Credentials

```
Admin Account:
Username: admin
Password: admin123
```
⚠️ **IMPORTANT**: Change the default password immediately after first login!

## 🛠️ Built With

- **Backend**
  - PHP 8.0
  - MySQL/MariaDB
  - Composer

- **Frontend**
  - AdminLTE 3
  - Bootstrap 4
  - jQuery
  - SweetAlert2
  - DataTables

- **Additional Tools**
  - PHPSpreadsheet (Excel handling)
  - TCPDF (PDF generation)

## 🔒 Security Features

- Secure password hashing (Bcrypt)
- Session-based authentication
- CSRF protection
- XSS prevention
- Input validation and sanitization
- Prepared SQL statements
---
<div align="center">
    <p>Made with ❤️ by <a href="https://github.com/vincecxz">Astro</a> for CTU Student Affairs Office</p>
</div> 