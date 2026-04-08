<?php
include "koneksi.php";
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    die("Akses ditolak!");
}

$barang = null;
$message = "";

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $koneksi->prepare("SELECT * FROM barang WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $barang = $result->fetch_assoc();
    }
    $stmt->close();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && $barang) {
    $kode = $_POST['kode_barang'] ?? '';
    $nama = $_POST['nama_barang'] ?? '';
    $harga = intval($_POST['harga'] ?? 0);
    $stok = intval($_POST['stok'] ?? 0);
    $id = $barang['id'];

    if (!empty($kode) && !empty($nama) && $harga > 0 && $stok >= 0) {
        $stmt = $koneksi->prepare("UPDATE barang SET kode_barang = ?, nama_barang = ?, harga = ?, stok = ? WHERE id = ?");
        $stmt->bind_param("ssiii", $kode, $nama, $harga, $stok, $id);
        
        if ($stmt->execute()) {
            $message = "✅ Barang berhasil diupdate!";
            $_GET['id'] = $id;
            $stmt->close();
            
            $stmt = $koneksi->prepare("SELECT * FROM barang WHERE id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            $barang = $result->fetch_assoc();
        } else {
            $message = "❌ Gagal update barang!";
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Barang - Kasir</title>
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
                <h2>Edit Barang</h2>
            </div>
        </div>

        <?php if ($barang): ?>
        <div class="card">
        <form method="post">
            <input type="text" name="kode_barang" placeholder="Kode Barang" required value="<?php echo htmlspecialchars($barang['kode_barang']); ?>">
            <input type="text" name="nama_barang" placeholder="Nama Barang" required value="<?php echo htmlspecialchars($barang['nama_barang']); ?>">
            <input type="number" name="harga" placeholder="Harga" min="0" required value="<?php echo $barang['harga']; ?>">
            <input type="number" name="stok" placeholder="Stok" min="0" required value="<?php echo $barang['stok']; ?>">
            <button type="submit">Simpan Perubahan</button>
        </form>
        </div>
        <?php else: ?>
        <div class="card">
            <p style="color: red;">Barang tidak ditemukan!</p>
        </div>
        <?php endif; ?>
    
    <a href="lihat_barang.php">← Kembali</a>
</body>
</html>
