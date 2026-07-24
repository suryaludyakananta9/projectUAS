-- =========================================================
-- Database: db_toko_elektronik
-- Sistem Informasi Toko Surya Elektronik (HP, Laptop, PC)
-- UAS Pemrograman Web Dasar - Kelas 2C
-- =========================================================

CREATE DATABASE IF NOT EXISTS db_toko_elektronik;
USE db_toko_elektronik;

-- ---------------------------------------------------------
-- Tabel users (untuk login admin)
-- ---------------------------------------------------------
CREATE TABLE users (
    id_user INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    nama_lengkap VARCHAR(100) NOT NULL
) ENGINE=InnoDB;

-- ---------------------------------------------------------
-- Tabel kategori (jenis produk: Handphone / Laptop / PC)
-- ---------------------------------------------------------
CREATE TABLE kategori (
    id_kategori INT AUTO_INCREMENT PRIMARY KEY,
    nama_kategori VARCHAR(50) NOT NULL
) ENGINE=InnoDB;

-- ---------------------------------------------------------
-- Tabel produk (berelasi ke kategori lewat id_kategori)
-- ---------------------------------------------------------
CREATE TABLE produk (
    id_produk INT AUTO_INCREMENT PRIMARY KEY,
    id_kategori INT NOT NULL,
    merek VARCHAR(50) NOT NULL,
    nama_produk VARCHAR(150) NOT NULL,
    spesifikasi TEXT,
    harga DECIMAL(12,2) NOT NULL DEFAULT 0,
    stok INT NOT NULL DEFAULT 0,
    gambar VARCHAR(255),
    deskripsi TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_produk_kategori FOREIGN KEY (id_kategori)
        REFERENCES kategori(id_kategori)
        ON DELETE RESTRICT
        ON UPDATE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------
-- Data awal kategori
-- ---------------------------------------------------------
INSERT INTO kategori (nama_kategori) VALUES
('Handphone'),
('Laptop'),
('PC / Komputer');

-- ---------------------------------------------------------
-- Data contoh produk
-- ---------------------------------------------------------
INSERT INTO produk (id_kategori, merek, nama_produk, spesifikasi, harga, stok, gambar, deskripsi) VALUES
(1, 'Samsung', 'Galaxy A55 5G', 'RAM 8GB, Storage 256GB, Layar 6.6" Super AMOLED', 5999000, 12, NULL, 'HP mid-range dengan kamera 50MP dan baterai 5000mAh.'),
(1, 'Apple', 'iPhone 14', 'RAM 6GB, Storage 128GB, Chip A15 Bionic', 11999000, 4, NULL, 'iPhone dengan performa tinggi dan kamera dual 12MP.'),
(1, 'Xiaomi', 'Redmi Note 13 Pro', 'RAM 8GB, Storage 256GB, Layar AMOLED 120Hz', 3699000, 20, NULL, 'HP dengan kamera 200MP dan fast charging 67W.'),
(2, 'ASUS', 'ROG Strix G16', 'Intel Core i7-13700H, RAM 16GB, SSD 512GB, RTX 4060', 22999000, 3, NULL, 'Laptop gaming dengan performa tinggi untuk gaming dan editing.'),
(2, 'Lenovo', 'ThinkPad E14', 'Intel Core i5-1235U, RAM 8GB, SSD 512GB', 9499000, 8, NULL, 'Laptop bisnis ringan dengan daya tahan baterai lama.'),
(2, 'Apple', 'MacBook Air M2', 'Chip M2, RAM 8GB, SSD 256GB', 16999000, 5, NULL, 'Laptop tipis dan ringan dengan performa chip Apple M2.'),
(3, 'Custom PC', 'Rakitan Gaming Ryzen 5', 'Ryzen 5 5600, RAM 16GB, SSD 512GB, RTX 3060', 13500000, 6, NULL, 'PC rakitan siap pakai untuk gaming dan produktivitas.'),
(3, 'HP', 'Pavilion Desktop TP01', 'Intel Core i5-12400, RAM 8GB, SSD 256GB', 8750000, 2, NULL, 'PC desktop untuk kebutuhan kerja dan belajar sehari-hari.');

-- ---------------------------------------------------------
-- User admin default
-- PENTING: kolom password di bawah masih PLACEHOLDER.
-- Jalankan config/generate_password.php lewat browser untuk
-- mendapatkan hash password asli dari "admin123", lalu
-- ganti nilai kolom password di bawah (via phpMyAdmin / query
-- UPDATE) dengan hash yang dihasilkan. Lihat README.md.
-- ---------------------------------------------------------
INSERT INTO users (username, password, nama_lengkap) VALUES
('admin', 'GANTI_DENGAN_HASIL_generate_password.php', 'Administrator Toko');
