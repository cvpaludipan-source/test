<?php
declare(strict_types=1);

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';

$pdo = Database::connection();

// Create tables
$pdo->exec(<<<SQL
CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(100) NOT NULL UNIQUE,
  full_name VARCHAR(150) NOT NULL,
  role ENUM('admin','staff') NOT NULL DEFAULT 'staff',
  password_hash VARCHAR(255) NOT NULL,
  email VARCHAR(190) NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
SQL);

$pdo->exec(<<<SQL
CREATE TABLE IF NOT EXISTS borrowers (
  id INT AUTO_INCREMENT PRIMARY KEY,
  full_name VARCHAR(150) NOT NULL,
  department VARCHAR(150) NULL,
  contact VARCHAR(150) NULL,
  email VARCHAR(190) NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
SQL);

$pdo->exec(<<<SQL
CREATE TABLE IF NOT EXISTS equipment (
  id INT AUTO_INCREMENT PRIMARY KEY,
  asset_tag VARCHAR(100) NOT NULL UNIQUE,
  name VARCHAR(190) NOT NULL,
  type VARCHAR(120) NOT NULL,
  status ENUM('available','borrowed','maintenance','lost') NOT NULL DEFAULT 'available',
  location VARCHAR(190) NULL,
  last_borrower_id INT NULL,
  notes TEXT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NULL DEFAULT NULL,
  CONSTRAINT fk_equipment_last_borrower FOREIGN KEY (last_borrower_id) REFERENCES borrowers(id) ON DELETE SET NULL
) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
SQL);

$pdo->exec(<<<SQL
CREATE TABLE IF NOT EXISTS borrow_transactions (
  id INT AUTO_INCREMENT PRIMARY KEY,
  equipment_id INT NOT NULL,
  borrower_id INT NOT NULL,
  purpose VARCHAR(255) NULL,
  checkout_at DATETIME NOT NULL,
  due_at DATETIME NULL,
  return_at DATETIME NULL,
  returned_condition VARCHAR(255) NULL,
  handled_by INT NULL,
  notes TEXT NULL,
  CONSTRAINT fk_bt_equipment FOREIGN KEY (equipment_id) REFERENCES equipment(id) ON DELETE CASCADE,
  CONSTRAINT fk_bt_borrower FOREIGN KEY (borrower_id) REFERENCES borrowers(id) ON DELETE RESTRICT,
  CONSTRAINT fk_bt_user FOREIGN KEY (handled_by) REFERENCES users(id) ON DELETE SET NULL,
  INDEX (equipment_id), INDEX(borrower_id)
) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
SQL);

$pdo->exec(<<<SQL
CREATE TABLE IF NOT EXISTS damage_loss_reports (
  id INT AUTO_INCREMENT PRIMARY KEY,
  equipment_id INT NOT NULL,
  transaction_id INT NULL,
  type ENUM('damage','loss') NOT NULL,
  description TEXT NULL,
  reported_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  reported_by INT NULL,
  resolved TINYINT(1) NOT NULL DEFAULT 0,
  CONSTRAINT fk_dl_equipment FOREIGN KEY (equipment_id) REFERENCES equipment(id) ON DELETE CASCADE,
  CONSTRAINT fk_dl_tx FOREIGN KEY (transaction_id) REFERENCES borrow_transactions(id) ON DELETE SET NULL,
  CONSTRAINT fk_dl_user FOREIGN KEY (reported_by) REFERENCES users(id) ON DELETE SET NULL
) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
SQL);

$pdo->exec(<<<SQL
CREATE TABLE IF NOT EXISTS notifications (
  id INT AUTO_INCREMENT PRIMARY KEY,
  type VARCHAR(50) NOT NULL,
  subject VARCHAR(190) NOT NULL,
  body TEXT NOT NULL,
  recipient VARCHAR(190) NULL,
  is_read TINYINT(1) NOT NULL DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
SQL);

// Seed default admin if not exists
$stmt = $pdo->prepare('SELECT COUNT(*) FROM users WHERE role = ?');
$stmt->execute(['admin']);
$hasAdmin = (int)$stmt->fetchColumn() > 0;
if (!$hasAdmin) {
    $passwordHash = password_hash('admin123', PASSWORD_DEFAULT);
    $stmt = $pdo->prepare('INSERT INTO users (username, full_name, role, password_hash, email) VALUES (?,?,?,?,?)');
    $stmt->execute(['admin', 'ICT Head', 'admin', $passwordHash, 'ict-head@example.com']);
    echo "Created default admin (username: admin / password: admin123)\n";
}

echo "Migration completed.\n";

