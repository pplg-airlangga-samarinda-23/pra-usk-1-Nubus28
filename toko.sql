-- Tabel user untuk login
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin','petugas') NOT NULL
);

-- Tabel barang
CREATE TABLE barang (
    id INT AUTO_INCREMENT PRIMARY KEY,
    kode_barang VARCHAR(10) NOT NULL UNIQUE,
    nama_barang VARCHAR(100) NOT NULL,
    harga INT NOT NULL
);

-- Tabel transaksi (header)
CREATE TABLE transaksi (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tanggal TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    total INT NOT NULL,
    petugas_id INT,
    FOREIGN KEY (petugas_id) REFERENCES users(id)
);

-- Tabel detail transaksi (item per transaksi)
CREATE TABLE transaksi_detail (
    id INT AUTO_INCREMENT PRIMARY KEY,
    transaksi_id INT,
    barang_id INT,
    jumlah INT NOT NULL,
    subtotal INT NOT NULL,
    FOREIGN KEY (transaksi_id) REFERENCES transaksi(id),
    FOREIGN KEY (barang_id) REFERENCES barang(id)
);

-- Insert data user
INSERT INTO users (username, password, role) VALUES 
('admin', 'admin123', 'admin'),
('petugas1', 'petugas123', 'petugas');

-- Insert data barang sample
INSERT INTO barang (kode_barang, nama_barang, harga) VALUES 
('BRG001', 'Mie Instan', 3000),
('BRG002', 'Teh Pucuk', 5000),
('BRG003', 'Rokok Sampoerna', 25000),
('BRG004', 'Sabun Lifebuoy', 8000),
('BRG005', 'Pasta Gigi Sensodyne', 18000);
