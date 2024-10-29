-- Membuat database jika belum ada
CREATE DATABASE IF NOT EXISTS certificate_validation;

-- Menggunakan database yang baru dibuat
USE certificate_validation;

-- Membuat tabel valid_certificates
CREATE TABLE IF NOT EXISTS valid_certificates (
    id INT AUTO_INCREMENT PRIMARY KEY,
    certificate_name VARCHAR(255) NOT NULL,
    md5_hash CHAR(32) NOT NULL,
    upload_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    issuer VARCHAR(255),
    issue_date DATE,
    expiry_date DATE,
    certificate_type VARCHAR(50),
    additional_info TEXT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Membuat indeks pada kolom md5_hash untuk optimasi pencarian
CREATE INDEX idx_md5_hash ON valid_certificates (md5_hash);

-- Menambahkan beberapa data sampel (opsional)
INSERT INTO valid_certificates (certificate_name, md5_hash, issuer, issue_date, expiry_date, certificate_type) VALUES
('Sample Certificate 1', 'a1b2c3d4e5f6g7h8i9j0k1l2m3n4o5p6', 'Sample University', '2023-01-01', '2028-01-01', 'Degree'),
('Sample Certificate 2', '1a2b3c4d5e6f7g8h9i0j1k2l3m4n5o6p', 'Tech Institute', '2023-02-15', '2024-02-15', 'Course Completion'),
('Sample Certificate 3', 'p6o5n4m3l2k1j0i9h8g7f6e5d4c3b2a1', 'Professional Body', '2023-03-30', '2026-03-30', 'Professional Certification');

-- Membuat user untuk aplikasi (ganti 'your_username' dan 'your_password' dengan nilai yang aman)
CREATE USER IF NOT EXISTS 'cert_validator'@'localhost' IDENTIFIED BY 'your_password';

-- Memberikan hak akses ke user untuk database certificate_validation
GRANT ALL PRIVILEGES ON certificate_validation.* TO 'cert_validator'@'localhost';

-- Memperbarui hak akses
FLUSH PRIVILEGES;