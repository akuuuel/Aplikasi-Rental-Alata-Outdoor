CREATE DATABASE IF NOT EXISTS rental_outdoor;
USE rental_outdoor;

CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    nama_lengkap VARCHAR(100) NOT NULL,
    role ENUM('admin', 'staff') DEFAULT 'staff'
);

CREATE TABLE barang (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nama_barang VARCHAR(100) NOT NULL,
    kategori VARCHAR(50) NOT NULL,
    stok INT NOT NULL DEFAULT 0,
    harga_sewa DECIMAL(10, 2) NOT NULL,
    foto VARCHAR(255),
    status ENUM('tersedia', 'kosong') DEFAULT 'tersedia'
);

CREATE TABLE pelanggan (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nama VARCHAR(100) NOT NULL,
    alamat TEXT,
    no_telp VARCHAR(15),
    ktp VARCHAR(20)
);

CREATE TABLE transaksi (
    id INT PRIMARY KEY AUTO_INCREMENT,
    id_pelanggan INT,
    tgl_sewa DATE NOT NULL,
    tgl_kembali DATE NOT NULL,
    total_bayar DECIMAL(10, 2) DEFAULT 0,
    status_transaksi ENUM('disewa', 'kembali') DEFAULT 'disewa',
    FOREIGN KEY (id_pelanggan) REFERENCES pelanggan(id) ON DELETE CASCADE
);

CREATE TABLE detail_transaksi (
    id INT PRIMARY KEY AUTO_INCREMENT,
    id_transaksi INT,
    id_barang INT,
    qty INT NOT NULL,
    subtotal DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (id_transaksi) REFERENCES transaksi(id) ON DELETE CASCADE,
    FOREIGN KEY (id_barang) REFERENCES barang(id) ON DELETE CASCADE
);

CREATE TABLE pengembalian (
    id INT PRIMARY KEY AUTO_INCREMENT,
    id_transaksi INT,
    tgl_dikembalikan DATE NOT NULL,
    denda DECIMAL(10, 2) DEFAULT 0,
    FOREIGN KEY (id_transaksi) REFERENCES transaksi(id) ON DELETE CASCADE
);

-- Insert default admin (password: password)
INSERT INTO users (username, password, nama_lengkap, role) VALUES 
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrator', 'admin');
