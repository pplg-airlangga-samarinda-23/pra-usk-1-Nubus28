<?php
include "koneksi.php";
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    die("Akses ditolak!");
}

$message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $kode = $_POST['kode_barang'] ?? '';
    $nama = $_POST['nama_barang'] ?? '';
    $harga = intval($_POST['harga'] ?? 0);
    $stok = intval($_POST['stok'] ?? 0);

    if (!empty($kode) && !empty($nama) && $harga > 0 && $stok >= 0) {
        $stmt = $koneksi->prepare("INSERT INTO barang (kode_barang, nama_barang, harga, stok) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssii", $kode, $nama, $harga, $stok);
        
        if ($stmt->execute()) {
            $message = "✅ Barang berhasil ditambahkan!";
            $_POST = [];
        } else {
            if (strpos($stmt->error, 'Duplicate') !== false) {
                $message = "❌ Kode barang sudah ada!";
            } else {
                $message = "❌ Gagal menambah barang: " . $stmt->error;
            }
        }
        $stmt->close();
    } else {
        $message = "❌ Semua field harus diisi dengan benar!";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Barang - Kasir</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="content-page">
    <div class="container">
        <div class="header">
            <div class="logo-box">
                <svg width="50" height="50" viewBox="0 0 100 100" style="filter: drop-shadow(0 1px 2px rgba(0,0,0,0.2));">
                    <circle cx="50" cy="50" r="45" fill="rgba(255,255,255,0.2)" stroke="white" stroke-width="2"/>
                    <rect x="25" y="35" width="50" height="35" rx="3" fill="white" opacity="0.9"/>
                    <rect x="25" y="35" width="50" height="15" rx="3" fill="rgba(255,255,255,0.6)"/>
                    <rect x="40" y="50" width="8" height="20" fill="rgba(168,213,186,0.8)"/>
                    <circle cx="48" cy="60" r="1.5" fill="white"/>
                    <rect x="52" y="50" width="8" height="20" fill="rgba(168,213,186,0.8)"/>
                    <circle cx="56" cy="60" r="1.5" fill="white"/>
                    <rect x="30" y="38" width="6" height="6" fill="rgba(200,230,215,0.7)"/>
                    <rect x="40" y="38" width="6" height="6" fill="rgba(200,230,215,0.7)"/>
                    <rect x="50" y="38" width="6" height="6" fill="rgba(200,230,215,0.7)"/>
                    <rect x="60" y="38" width="6" height="6" fill="rgba(200,230,215,0.7)"/>
                </svg>
            </div>
            <div class="header-content">
                <h2>Tambah Barang Baru</h2>
            </div>
        </div>

        <div class="card">
        <form method="post">
            <input type="text" name="kode_barang" placeholder="Kode Barang (cth: BRG001)" required value="<?php echo $_POST['kode_barang'] ?? ''; ?>">
            <input type="text" name="nama_barang" placeholder="Nama Barang (cth: Mie Instan)" required value="<?php echo $_POST['nama_barang'] ?? ''; ?>">
            <input type="number" name="harga" placeholder="Harga (cth: 5000)" min="0" required value="<?php echo $_POST['harga'] ?? ''; ?>">
            <input type="number" name="stok" placeholder="Stok awal" min="0" required value="<?php echo $_POST['stok'] ?? '0'; ?>">
            <button type="submit">Simpan Barang</button>
        </form>
    </div>
    
    <a href="lihat_barang.php">← Kembali</a>
</body>
</html>
